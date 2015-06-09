<?php

namespace BoomCMS\Core\Person;

use BoomCMS\Core\Group;
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
        $this->app->singleton('boomcms.person.provider', function ($app) {
            return new Provider();
        });

        $this->app->singleton('boomcms.group.provider', function ($app) {
            return new Group\Provider();
        });
    }
}
