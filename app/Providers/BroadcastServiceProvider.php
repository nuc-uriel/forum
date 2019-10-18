<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

//        request(base_path('routes/channels.php'));

        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel('App.User.*', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });

        Broadcast::channel('inform.{code}', function ($user, $code) {
            return $user->code === $code;
        });

        Broadcast::channel('chat.{code}', function ($user, $code) {
            return $user->code === $code;
        });

//        Broadcast::channel('bs.*', function ($user, $id) {
//            return $user->id == $id;
//        });

    }
}
