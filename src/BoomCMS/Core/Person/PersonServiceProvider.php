<?php

namespace BoomCMS\Core\Person;

use Illuminate\Support\ServiceProvider;

class PersonServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('BoomPersonProvider', function($app)
        {
            return new Provider();
        });
    }
}