<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $auth = new Auth\Auth($this->app['session'],
            $this->app['boomcms.person.provider'],
            new Auth\PermissionsProvider(),
            $this->app['cookie']
        );

        $this->app->singleton('boomcms.auth', function ($app) use ($auth) {
            return $auth;
        });
    }

    /**
     *
     * @return void
     */
    public function register()
    {
    }

}
