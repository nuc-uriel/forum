<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/5 0005 0024
 * Time: 16:31
 */

namespace App;

use function foo\func;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Comment
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Like[] $likes
 * @property-read \App\Topic $topic
 * @mixin \Eloquent
 */
class Comment extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_DELETED = 1;

    const TYPE_COMMENT = 0; // 回复主题
    const TYPE_REPLY = 1;   // 回复评论

    protected $table = 'comment';
    protected $primaryKey = 'id';
    protected $fillable = array('u_id', 't_id', 'parent_id', 'content', 'image', 'type', 'status');

    public function comments()
    {
        return $this->hasMany('App\Comment', 'parent_id', 'id')->where('status', Comment::STATUS_NORMAL)->where('type', Comment::TYPE_REPLY);
    }

    public function likes()
    {
        return $this->hasMany('App\Like', 'target_id', 'id')->where('status', Like::STATUS_NORMAL)->where('type', Like::TYPE_COMMENT);
    }

    public function topic()
    {
        return $this->belongsTo('App\Topic', 't_id');
    }

    public function creator()
    {
        return $this->belongsTo('App\User', 'u_id');
    }

    public function parent()
    {
        return $this->belongsTo('App\Comment', 'parent_id');
    }

    public function getComments()
    {
        $comments = new Collection();
        $func = function ($comment) use (&$func, $comments) {
            foreach ($comment->comments as $com) {
                $comments->push($com);
                $func($com);
            }
        };
        $func($this);
        return $comments;
    }
}
