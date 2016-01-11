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
        $this->app->singleton('boomcms.repositories.asset', function () {
            return new Repositories\Asset(new Models\Asset(), new Models\AssetVersion());
        });

        $this->app->singleton('boomcms.repositories.page', function () {
            return new Repositories\Page(new Models\Page());
        });

        $this->app->singleton('boomcms.repositories.person', function () {
            return new Repositories\Person(new Models\Person());
        });

        $this->app->singleton('boomcms.repositories.group', function () {
            return new Repositories\Group();
        });

        $this->app->singleton('boomcms.repositories.tag', function () {
            return new Repositories\Tag(new Models\Tag());
        });

        $this->app->singleton('boomcms.repositories.template', function () {
            return new Repositories\Template(new Models\Template());
        });

        $this->app->singleton('boomcms.repositories.url', function () {
            return new Repositories\URL(new Models\URL());
        });

        $this->app->singleton(Repositories\Site::class, function() {
            return new Repositories\Site(new Models\Site());
        });
    }
}
