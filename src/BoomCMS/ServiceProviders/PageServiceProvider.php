<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Page;
use BoomCMS\Repositories\Page as Repository;
use Illuminate\Support\ServiceProvider;

class PageServiceProvider extends ServiceProvider
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
        $this->app->singleton('boomcms.repositories.page', function ($app) {
            return new Repository(new Page());
        });
    }
}
