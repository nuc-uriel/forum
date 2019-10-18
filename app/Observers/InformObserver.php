<?php


namespace App\Observers;


use App\Inform;
use App\Events\SystemInform;

class InformObserver
{
    public function created(Inform $inform)
    {
        event(new \App\Events\SystemInform($inform));
        return true;
    }
}