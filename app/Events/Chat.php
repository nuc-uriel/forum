<?php

namespace App\Events;

use App\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Chat implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    /**
     * Create a new event instance.
     * @param Message $message
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    public $broadcastQueue = 'my-broadcast';

    public function broadcastWith(){
        return array(
            'message'=>$this->message,
            'user'=>$this->message->sender
        );
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.'.$this->message->receiver->code);
    }
}
