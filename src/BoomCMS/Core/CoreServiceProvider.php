<?php

namespace BoomCMS\Core;

use BoomCMS\Core\Asset;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class CoreServiceProvider extends ServiceProvider
{
    /**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
    public function boot(Router $router, Asset\Provider $assetProvider)
    {
        $this->loadViewsFrom(__DIR__ . '/../../views/boom', 'boom');

        $router->pattern('asset', '[0-9]+');
        $router->bind('asset', function($assetId) use ($assetProvider) {
            return $assetProvider->findById($assetId);
        });

        include __DIR__ . '/../../routes.php';
    }

    /**
	 *
	 * @return void
	 */
    public function register()
    {
    }

}
