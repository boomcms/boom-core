<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories;
use BoomCMS\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot(Router $router)
    {
        $this->app->singleton(Repositories\Asset::class, function () {
            return new Repositories\Asset(new Models\Asset(), new Models\AssetVersion());
        });

        $this->app->singleton(Repositories\Page::class, function () use ($router) {
            return new Repositories\Page(new Models\Page(), $router->getActiveSite());
        });

        $this->app->singleton(Repositories\PageVersion::class, function () {
            return new Repositories\PageVersion(new Models\PageVersion());
        });

        $this->app->singleton(Repositories\Person::class, function () use ($router) {
            return new Repositories\Person(new Models\Person(), $router->getActiveSite());
        });

        $this->app->singleton(Repositories\Group::class, function () {
            return new Repositories\Group();
        });

        $this->app->singleton(Repositories\Tag::class, function () use ($router) {
            return new Repositories\Tag(new Models\Tag(), $router->getActiveSite());
        });

        $this->app->singleton(Repositories\Template::class, function () {
            return new Repositories\Template(new Models\Template());
        });

        $this->app->singleton(Repositories\URL::class, function () use ($router) {
            return new Repositories\URL(new Models\URL(), $router->getActiveSite());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Repositories\Site::class, function () {
            return new Repositories\Site(new Site());
        });
    }
}
