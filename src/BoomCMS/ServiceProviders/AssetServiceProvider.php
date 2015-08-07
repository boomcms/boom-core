<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Asset\Provider;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

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
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.asset.provider', function ($app) {
            return new Provider();
        });
    }
}
