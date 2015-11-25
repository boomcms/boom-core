<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\URL as URLModel;
use BoomCMS\Repositories\URL;
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
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.repositories.url', function ($app) {
            return new URL(new URLModel());
        });
    }
}
