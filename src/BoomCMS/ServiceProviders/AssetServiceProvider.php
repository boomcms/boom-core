<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Repositories\Asset;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class AssetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router, Asset $assetRepository)
    {
        $router->pattern('asset', '[0-9]+');
        $router->bind('asset', function ($assetId) use ($assetRepository) {
            return $assetRepository->findById($assetId);
        });
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton('boomcms.repositories.asset', function ($app) {
            return new Asset();
        });
    }
}
