<?php

namespace BoomCMS\Core;

use BoomCMS\Core\Asset;
use BoomCMS\Core\Page;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CoreServiceProvider extends ServiceProvider
{
    /**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
    public function boot(Router $router, Asset\Provider $assetProvider, Page\Provider $pageProvider)
    {
        $this->loadViewsFrom(__DIR__ . '/../../views/boom', 'boom');
        $this->loadViewsFrom(__DIR__ . '/../../views/chunks', 'boomcms.chunks');
		$this->loadTranslationsFrom(__DIR__ . '/../../lang', 'boom');

        $router->pattern('asset', '[0-9]+');
        $router->bind('asset', function($assetId) use ($assetProvider) {
            return $assetProvider->findById($assetId);
        });

        $router->pattern('page', '[0-9]+');
        $router->bind('page', function($pageId) use ($pageProvider) {
            $page = $pageProvider->findById($pageId);

            if ( !$page->loaded()) {
                throw new NotFoundHttpException();
            }

            return $page;
        });

        $this->publishes([__DIR__ . '/../../../public' => public_path('vendor/boomcms/boom-core')], 'boomcms');

        include __DIR__ . '/../../routes.php';
    }

    /**
	 *
	 * @return void
	 */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/boomcms.php', 'boomcms');
    }
}
