<?php


namespace App\Providers;

use App\User;
use Illuminate\Auth\AuthServiceProvider;

class MyAuthServiceProvider extends AuthServiceProvider
{
    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerRequestRebindHandler()
    {
        $this->app->rebinding('request', function ($app, $request) {
            $request->setUserResolver(function ($guard = null) {
                if (session('uid', '')) {
                    return User::find(session('uid'));
                } else {
                    return null;
                }
            });
        });
    }
}
