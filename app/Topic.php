<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/4/24 0024
 * Time: 11:28
 */

namespace App;


use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Topic extends Model
{
    use Searchable;
    const STATUS_NORMAL = 0;
    const STATUS_BAN = 1;
    const STATUS_DELETED = 2;

    const IS_TOP_FALSE = 0;
    const IS_TOP_TRUE = 1;

    const CAN_COMMENT_TRUE = 0;
    const CAN_COMMENT_FALSE = 1;

    protected $table = 'topic';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 'g_id', 'title', 'content', 'is_top', 'can_comment', 'status');

    public function comments()
    {
        return $this->hasMany('App\Comment', 'parent_id', 'id')->where('status', Comment::STATUS_NORMAL)->where('type', Comment::TYPE_COMMENT);
    }

    public function goodComments()
    {
        return $this->comments()->has('likes')->withCount('likes')->get()->sortByDesc('likes_count');
    }

    public function likes()
    {
        return $this->hasMany('App\Like', 'target_id', 'id')->where('status', Like::STATUS_NORMAL)->where('type', Like::TYPE_TOPIC);
    }

    public function collects()
    {
        return $this->hasMany('App\Collect', 't_id', 'id')->where('status', Collect::STATUS_NORMAL);
    }

    public function group()
    {
        return $this->belongsTo('App\Group', 'g_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }

    // 获取主题第一张图片
    public function firstImg()
    {
        preg_match_all('/<img.*?src=["|\'](.+?)["|\'].*?>/', $this->content, $images, PREG_SET_ORDER);
        if ($images) {
            return $images[0][1];
        } else {
            return false;
        }
    }

    public function searchableAs()
    {
        return 'topic';
    }

    public function toSearchableArray()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'content' => strip_tags($this->content),
            'g_id' => $this->g_id,
            'status' => $this->status,
            'created_at' => $this->created_at,
        );
    }
}