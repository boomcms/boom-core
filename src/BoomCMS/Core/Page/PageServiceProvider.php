<?php

namespace BoomCMS\Core\Page;

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
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.page.provider', function ($app) {
            return new Provider($app['boomcms.editor']);
        });
    }
}
