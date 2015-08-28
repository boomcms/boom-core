<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Page;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Router $router, Page\Provider $pageProvider)
    {
        $this->loadViewsFrom(__DIR__.'/../../views/boom', 'boom');
        $this->loadViewsFrom(__DIR__.'/../../views/chunks', 'boomcms.chunks');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'boom');

        $router->pattern('page', '[0-9]+');
        $router->bind('page', function ($pageId) use ($pageProvider) {
            $page = $pageProvider->findById($pageId);

            if (!$page->loaded()) {
                throw new NotFoundHttpException();
            }

            return $page;
        });

        $this->publishes([
            __DIR__.'/../../../public'           => public_path('vendor/boomcms/boom-core'),
            __DIR__.'/../../database/migrations' => base_path('/migrations/boomcms'),
        ], 'boomcms');

        include __DIR__.'/../../routes.php';
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/menu.php', 'boomcms.menu');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/text_editor_toolbar.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/viewHelpers.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/settingsManagerOptions.php', 'boomcms');
    }
}
