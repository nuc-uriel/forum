<?php
/**
 * Created by PhpStorm.
 * User: uriel
 * Date: 2019/5/7 0007 0024
 * Time: 20:23
 */

namespace App\Observers;

use App\Comment;
use App\Like;

class CommentObserver
{
    public function updated(Comment $comment)
    {
        if ($comment->status == Comment::STATUS_DELETED) {
            $comment->comments()->update(array('status'=>Comment::STATUS_DELETED));
            $comment->likes()->update(array('status'=>Like::STATUS_DELETED));
        }
    }
}
