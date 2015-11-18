<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Repositories;
use BoomCMS\Database\Models\Person;
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
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.repositories.person', function ($app) {
            return new Repositories\Person(new Person());
        });

        $this->app->singleton('boomcms.repositories.group', function ($app) {
            return new Repositories\Group();
        });
    }
}
