<?php

namespace App\Listeners;

use App\Events\SystemInform;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SystemInformListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SystemInform  $event
     * @return void
     */
    public function handle(SystemInform $event)
    {
        //
    }
}
