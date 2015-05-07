<?php

namespace BoomCMS\Core\Asset;

use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
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
        $this->app->singleton('boomcms.asset.provider', function ($app) {
            return new Provider();
        });
    }
}
