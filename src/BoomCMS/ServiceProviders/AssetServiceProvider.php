<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Repositories\Asset as Repository;
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
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.repositories.asset', function ($app) {
            return new Repository(new Asset(), new AssetVersion());
        });
    }
}
