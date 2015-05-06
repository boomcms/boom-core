<?php

namespace BoomCMS\Core\Auth;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $auth = new Auth($this->app['session'], $this->app['boomcms.person.provider'], new PermissionsProvider());

        $this->app->singleton('boomcms.auth', function ($app) use ($auth) {
            return $auth;
        });

        View::share('auth', $auth);
    }

    /**
     *
     * @return void
     */
    public function register()
    {
    }

}
