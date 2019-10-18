<?php

namespace App\Events;

use App\Inform;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SystemInform implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $inform;
    /**
     * Create a new event instance.
     * @param Inform $inform
     * @return void
     */
    public function __construct($inform)
    {
        $this->inform = $inform;
    }

    public $broadcastQueue = 'my-broadcast';

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('inform.'.$this->inform->to->code);
    }

//    public function broadcastWith(){
//        return ['id'=>$this->inform->id];
//    }

}
