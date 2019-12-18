<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/24 0024
 * Time: 15:35
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use function PHPSTORM_META\type;

/**
 * App\Group
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $admin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $allMember
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $blacklist
 * @property-read \App\User $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $friendshipAsFriend
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $friendshipAsOwner
 * @property-read \App\GroupType $groupType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GroupLabel[] $labels
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $leader
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $member
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $waitConfirm
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GroupBan[] $banWords
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GroupLog[] $logs
 */
class Group extends Model
{
    use Searchable;
    const STATUS_NORMAL = 0;
    const STATUS_WAIT_CONFIRM = 1;
    const STATUS_NOT_PASS = 2;
    const STATUS_BANNED = 3;
    const STATUS_DELETED = 4;

    const JOIN_WAY_ALLOW = 0;
    const JOIN_WAY_APPLY = 1;
    const JOIN_WAY_NOT_ALLOW = 2;

    const MAX_FRIEND = 4;

    protected $table = 'group';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 'gt_id', 'name', 'icon', 'introduce', 'admin_as', 'member_as', 'join_way', 'status');

    //  创建者
    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }

    // 类型
    public function type(){
        return $this->belongsTo('App\GroupType', 'gt_id');
    }

    //  组长
    public function leader()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivot('status', GroupMember::STATUS_NORMAL)->wherePivot('role', GroupMember::ROLE_LEADER)->withPivot('id');
    }

    //  管理员
    public function admin()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivot('status', GroupMember::STATUS_NORMAL)->wherePivot('role', GroupMember::ROLE_ADMIN)->withPivot('id');
    }

    //  成员
    public function member()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivot('status', GroupMember::STATUS_NORMAL)->wherePivot('role', GroupMember::ROLE_MEMBER);
    }

    //  黑名单
    public function blacklist()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivot('status', GroupMember::STATUS_BLACKLIST);
    }

    //  待审核成员
    public function waitConfirm()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivot('status', GroupMember::STATUS_WAIT_CONFIRM)->withPivot('id');
    }

    //  全部成员
    public function allMember()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivot('status', GroupMember::STATUS_NORMAL)->withPivot('id', 'role')->withTimestamps()->where('user.status', User::STATUS_NORMAL);
    }

    //  全部退出或拉黑成员
    public function outMember()
    {
        return $this->belongsToMany('App\User', 'group_member', 'g_id', 'u_id')->wherePivotIn('status', array(GroupMember::STATUS_BLACKLIST, GroupMember::STATUS_DELETED))->withPivot('id', 'role')->withTimestamps();
    }

    //  组类型
    public function groupType()
    {
        return $this->belongsTo('App\GroupType', 'gt_id');
    }

    //  组标签
    public function labels()
    {
        return $this->hasMany('App\GroupLabel', 'g_id', 'id')->where('status', GroupLabel::STATUS_NORMAL);
    }

    //  组违禁词
    public function banWords()
    {
        return $this->hasMany('App\GroupBan', 'g_id', 'id')->where('status', GroupBan::STATUS_NORMAL);
    }

    //  组日志
    public function logs()
    {
        return $this->hasMany('App\GroupLog', 'g_id', 'id')->where('status', GroupLog::STATUS_NORMAL)->where('created_at', '>=', strtotime("-2 month"))->orderBy('created_at', 'desc');
    }

    //  组日志
    public function getLogs($type = NULL)
    {
        if (in_array($type, array(GroupLog::TYPE_GROUP_MANAGE, GroupLog::TYPE_MEMBER_MANAGE, GroupLog::TYPE_TOPIC_MANAGE))) {
            return $this->logs->where('type', $type)->groupBy(function ($item, $key) {
                return $item->created_at->format('Y-m-d');
            });
        } else {
            return $this->logs->groupBy(function ($item, $key) {
                return $item->created_at->format('Y-m-d');
            });
        }
    }

    //  当前组作为主人的友情小组
    public function friendshipAsOwner()
    {
        return $this->belongsToMany('App\Group', 'group_friendship', 'go_id', 'gf_id')->wherePivot('status', GroupFriendship::STATUS_NORMAL)->withPivot('id');
    }

    //  当前组作为朋友的友情小组
    public function friendshipAsFriend()
    {
        return $this->belongsToMany('App\Group', 'group_friendship', 'gf_id', 'go_id')->wherePivot('status', GroupFriendship::STATUS_NORMAL)->withPivot('id');
    }

    //  全部友情小组
    public function friendship()
    {
        return $this->friendshipAsOwner->merge($this->friendshipAsFriend)->take(self::MAX_FRIEND);
    }

    // 全部讨论
    public function topics()
    {
        return $this->hasMany('App\Topic', 'g_id', 'id')->where('status', Topic::STATUS_NORMAL);
    }

    // 全部讨论
    public function banTopics()
    {
        return $this->hasMany('App\Topic', 'g_id', 'id')->where('status', Topic::STATUS_BAN);
    }

    // 全部评论
    public function comments()
    {
        return $this->hasManyThrough('App\Comment', 'App\Topic', 'g_id', 't_id')->whereRaw('`comment`.`status`=?', [Comment::STATUS_NORMAL]);
    }

    public function searchableAs()
    {
        return 'group';
    }

    public function toSearchableArray()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'status' => is_null($this->status) ? 0: $this->status,
            'labels' => $this->labels->pluck('name')->implode(' ')
        );
    }
}