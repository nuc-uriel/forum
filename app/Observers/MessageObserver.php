<?php


namespace App\Observers;


use App\Message;

class MessageObserver
{
    public function creating(Message $message)
    {
        $message->group_code = Message::getGroupCode($message->uf_id, $message->ut_id);
        return true;
    }

    public function created(Message $message)
    {
        event(new \App\Events\Chat($message));
        return true;
    }
}