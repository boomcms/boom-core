<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models;
use BoomCMS\Repositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
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
        $this->app->singleton('boomcms.repositories.asset', function ($app) {
            return new Repositories\Asset(new Models\Asset(), new Models\AssetVersion());
        });

        $this->app->singleton('boomcms.repositories.page', function ($app) {
            return new Repositories\Page(new Models\Page());
        });

        $this->app->singleton('boomcms.repositories.person', function ($app) {
            return new Repositories\Person(new Models\Person());
        });

        $this->app->singleton('boomcms.repositories.group', function ($app) {
            return new Repositories\Group();
        });

        $this->app->singleton('boomcms.repositories.tag', function ($app) {
            return new Repositories\Tag(new Models\Tag());
        });

        $this->app->singleton('boomcms.repositories.template', function ($app) {
            return new Repositories\Template(new Models\Template());
        });

        $this->app->singleton('boomcms.repositories.url', function ($app) {
            return new Repositories\URL(new Models\URL());
        });
    }
}
