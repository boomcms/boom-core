<?php

namespace BoomCMS\Core\Asset;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AssetServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router, Provider $assetProvider)
    {
        $router->pattern('asset', '[0-9]+');
        $router->bind('asset', function ($assetId) use ($assetProvider) {
            return $assetProvider->findById($assetId);
        });
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
