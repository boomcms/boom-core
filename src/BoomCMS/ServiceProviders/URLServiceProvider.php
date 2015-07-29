<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\URL;
use Illuminate\Support\ServiceProvider;

class URLServiceProvider extends ServiceProvider
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
        $this->app->singleton('boomcms.url.provider', function ($app) {
            return new URL\Provider();
        });
    }
}
