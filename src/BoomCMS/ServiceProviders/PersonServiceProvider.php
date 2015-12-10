<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Person;
use BoomCMS\Repositories;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class PersonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $router->model('group', Group::class);
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
