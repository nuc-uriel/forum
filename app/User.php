<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/17 0017
 * Time: 18:40
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class User extends Model
{
    use Searchable;
    const STATUS_NORMAL = 0;
    const STATUS_BANNED = 1;
    const STATUS_DELETED = 2;

    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $fillable = array('username', 'avatar', 'sex', 'age', 'place', 'email', 'password', 'signature', 'introduce', 'code', 'status', 'confirmation');

    public function createGroups()
    {
        return $this->hasMany('App\Group', 'u_id', 'id');
    }

    public function informs()
    {
        return $this->hasMany('App\Inform', 'ut_id', 'id');
    }

    public function joinGroups()
    {
        return $this->belongsToMany('App\Group', 'group_member', 'u_id', 'g_id')->wherePivot('status', GroupMember::STATUS_NORMAL)->withPivot('role')->where('group.status', Group::STATUS_NORMAL);
    }

    public function idol()
    {
        return $this->belongsToMany('App\User', 'member_relationship', 'myself_id', 'other_id')->wherePivot('status', MemberRelationship::STATUS_NORMAL)->wherePivot('type', MemberRelationship::TYPE_FANS)->withPivot('id');
    }

    public function fans()
    {
        return $this->belongsToMany('App\User', 'member_relationship', 'other_id', 'myself_id')->wherePivot('status', MemberRelationship::STATUS_NORMAL)->wherePivot('type', MemberRelationship::TYPE_FANS)->withPivot('id');
    }

    public function blacklist()
    {
        return $this->belongsToMany('App\User', 'member_relationship', 'myself_id', 'other_id')->wherePivot('status', MemberRelationship::STATUS_NORMAL)->wherePivot('type', MemberRelationship::TYPE_BLACKLIST)->withPivot('id');
    }

    public function beBlacklist()
    {
        return $this->belongsToMany('App\User', 'member_relationship', 'other_id', 'myself_id')->wherePivot('status', MemberRelationship::STATUS_NORMAL)->wherePivot('type', MemberRelationship::TYPE_BLACKLIST)->withPivot('id');
    }

    public function contacts()
    {
        $unread_msg = Message::select(\DB::raw('MAX(`id`) AS `id`,`group_code`'))->where('ut_id', $this->id)->where('status', Message::STATUS_UNREAD)->groupBy('group_code')->orderBy('id', 'desc')->get();
        $read_msg = Message::select(\DB::raw('MAX(`id`) AS `id`,`group_code`'))->where(function($query){
            $query->where(function ($query){
                $query->where('ut_id', $this->id)->whereIn('status', array(Message::STATUS_READ,Message::STATUS_SENDER_DELETED));
            })->orWhere(function ($query){
                $query->where('uf_id', $this->id)->whereNotIn('status', array(Message::STATUS_SENDER_DELETED,Message::STATUS_ALL_DELETED));
            });
        })->whereNotIn('group_code', $unread_msg->pluck('group_code'))->groupBy('group_code')->orderBy('id', 'desc')->get();
//        $read_msg = Message::select(\DB::raw('MAX(`id`) AS `id`,`group_code`'))->where('ut_id', $this->id)->whereIn('status', array(Message::STATUS_READ,Message::STATUS_SENDER_DELETED))->whereNotIn('group_code', $unread_msg->pluck('group_code'))->groupBy('group_code')->orderBy('id', 'desc')->get();
        $ids = $unread_msg->pluck('id')->merge($read_msg->pluck('id'));
         return Message::whereIn('id',$ids)->orderBy(\DB::raw('field(`id`, '.$ids->implode(',').')'));
    }

    public function getMessageForOne($uid)
    {
        $group_code = Message::getGroupCode($this->id, $uid);
        Message::where('group_code', $group_code)->where('ut_id', $this->id)->where('status', Message::STATUS_UNREAD)->update(array(
            'status' => Message::STATUS_READ
        ));
        return Message::where(function($query){
            $query->where(function ($query){
                $query->where('ut_id', $this->id)->whereIn('status', array(Message::STATUS_READ,Message::STATUS_SENDER_DELETED));
            })->orWhere(function ($query){
                $query->where('uf_id', $this->id)->whereNotIn('status', array(Message::STATUS_SENDER_DELETED,Message::STATUS_ALL_DELETED));
            });
        })->where('group_code', $group_code)->orderBy('created_at')->get();
//        return Message::where('group_code', $group_code)->where('status', Message::STATUS_READ)->orderBy('created_at')->get();
    }

    // 发表的所有帖子
    public function topicsAsSender(){
        return $this->hasMany('App\Topic', 'u_id', 'id')->where('status','!=', Topic::STATUS_DELETED);
    }

    // 发表的所有评论所在的帖子
    public function topicsAsCommenter(){
        return $this->belongsToMany('App\Topic', 'comment', 'u_id', 't_id')->wherePivot('status','!=', Comment::STATUS_DELETED)->withPivot('id', 'parent_id', 'content')->where('topic.status','!=', Topic::STATUS_DELETED);
    }

    // 所有点赞所在的帖子
    public function topicsAsLiker(){
        return $this->belongsToMany('App\Topic', 'like', 'u_id', 't_id')->wherePivot('status','!=', Like::STATUS_DELETED)->withPivot('id', 'target_id')->where('topic.status','!=', Topic::STATUS_DELETED);
    }

    // 所有收藏的帖子
    public function topicsAsCollector(){
        return $this->belongsToMany('App\Topic', 'collect', 'u_id', 't_id')->wherePivot('status','!=', Collect::STATUS_DELETED)->withPivot('id')->where('topic.status','!=', Topic::STATUS_DELETED);
    }

    // 所有加入小组的讨论
    public function topics()
    {
        return $this->hasManyThrough('App\Topic', 'App\Group', 'u_id', 'g_id')->where('topic.status', Topic::STATUS_NORMAL);
    }

    public function searchableAs()
    {
        return 'member';
    }

    public function toSearchableArray()
    {
        return array(
            'id' => $this->id,
            'username' => $this->username,
            'signature' => is_null($this->signature) ? '': $this->signature,
            'introduce' => is_null($this->introduce) ? '': $this->introduce,
            'status' => is_null($this->status) ? 0: $this->status
        );
    }
}