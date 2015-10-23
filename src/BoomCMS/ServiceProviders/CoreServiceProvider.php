<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Page;
use BoomCMS\ServiceProviders\AssetServiceProvider;
use BoomCMS\ServiceProviders\AuthServiceProvider;
use BoomCMS\ServiceProviders\ChunkServiceProvider;
use BoomCMS\ServiceProviders\EditorServiceProvider;
use BoomCMS\ServiceProviders\EventServiceProvider;
use BoomCMS\ServiceProviders\PageServiceProvider;
use BoomCMS\ServiceProviders\PersonServiceProvider;
use BoomCMS\ServiceProviders\SettingsServiceProvider;
use BoomCMS\ServiceProviders\TagServiceProvider;
use BoomCMS\ServiceProviders\TemplateServiceProvider;
use BoomCMS\ServiceProviders\URLServiceProvider;
use BoomCMS\Support\Facades;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CoreServiceProvider extends ServiceProvider
{
    protected $aliases = [
        'Asset'     => Facades\Asset::class,
        'Settings'  => Facades\Settings::class,
        'Chunk'     => Facades\Chunk::class,
        'Page'      => Facades\Page::class,
        'Editor'    => Facades\Editor::class,
        'Tag'       => Facades\Tag::class,
        'Template'  => Facades\Template::class,
        'Group'     => Facades\Group::class,
    ];

    protected $serviceProviders = [
        TemplateServiceProvider::class,
        AssetServiceProvider::class,
        PersonServiceProvider::class,
        AuthServiceProvider::class,
        EditorServiceProvider::class,
        PageServiceProvider::class,
        SettingsServiceProvider::class,
        ChunkServiceProvider::class,
        URLServiceProvider::class,
        TagServiceProvider::class,
        EventServiceProvider::class,
        HtmlServiceProvider::class,
    ];

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
        $this->registerAliases();

        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/menu.php', 'boomcms.menu');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/text_editor_toolbar.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/viewHelpers.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/settingsManagerOptions.php', 'boomcms');
    
        $this->registerServiceProviders();
    }

    private function registerAliases()
    {
        $loader = AliasLoader::getInstance();

        foreach ($this->aliases as $abstract => $alias) {
            $loader->alias($abstract, $alias);
        }
    }

    private function registerServiceProviders()
    {
        foreach ($this->serviceProviders as $provider) {
            $this->app->register($provider);
        }
    }
}
