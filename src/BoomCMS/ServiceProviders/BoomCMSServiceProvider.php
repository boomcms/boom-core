<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\ServiceProviders;
use BoomCMS\Support\Facades;
use BoomCMS\Support\Helpers\Asset;
use BoomCMS\Support\Str;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\ServiceProvider;

class BoomCMSServiceProvider extends ServiceProvider
{
    protected $aliases = [
        'Asset'       => Facades\Asset::class,
        'AssetHelper' => Asset::class,
        'Settings'    => Facades\Settings::class,
        'Chunk'       => Facades\Chunk::class,
        'Page'        => Facades\Page::class,
        'Editor'      => Facades\Editor::class,
        'Tag'         => Facades\Tag::class,
        'Template'    => Facades\Template::class,
        'Group'       => Facades\Group::class,
        'Str'         => Str::class,
    ];

    protected $serviceProviders = [
        ServiceProviders\TemplateServiceProvider::class,
        ServiceProviders\AssetServiceProvider::class,
        ServiceProviders\PersonServiceProvider::class,
        ServiceProviders\AuthServiceProvider::class,
        ServiceProviders\EditorServiceProvider::class,
        ServiceProviders\PageServiceProvider::class,
        ServiceProviders\SettingsServiceProvider::class,
        ServiceProviders\ChunkServiceProvider::class,
        ServiceProviders\URLServiceProvider::class,
        ServiceProviders\TagServiceProvider::class,
        ServiceProviders\EventServiceProvider::class,
        ServiceProviders\RouteServiceProvider::class,
        HtmlServiceProvider::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../../views/boomcms', 'boomcms');
        $this->loadViewsFrom(__DIR__.'/../../views/chunks', 'boomcms.chunks');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'boomcms');

        $this->publishes([
            __DIR__.'/../../../public'           => public_path('vendor/boomcms/boom-core'),
            __DIR__.'/../../database/migrations' => base_path('/migrations/boomcms'),
        ], 'boomcms');
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
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/assets.php', 'boomcms');

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
