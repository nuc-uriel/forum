<?php


namespace App\Observers;

use App\Collect;
use App\Comment;
use App\Like;
use App\Topic;

class TopicObserver
{
    public function updated(Topic $topic)
    {
        if ($topic->status == Topic::STATUS_DELETED) {
            $topic->comments()->update(array('status'=>Comment::STATUS_DELETED));
            $topic->likes()->update(array('status'=>Like::STATUS_DELETED));
            $topic->collects()->update(array('status'=>Collect::STATUS_DELETED));
        }
    }
}
