<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\BoomCMS;
use BoomCMS\ServiceProviders;
use BoomCMS\Support\Facades;
use BoomCMS\Support\Helpers\Asset;
use BoomCMS\Support\Str;
use Collective\Html\HtmlServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class BoomCMSServiceProvider extends ServiceProvider
{
    protected $aliases = [
        'Asset'       => Facades\Asset::class,
        'AssetHelper' => Asset::class,
        'BoomCMS'     => Facades\BoomCMS::class,
        'Settings'    => Facades\Settings::class,
        'Chunk'       => Facades\Chunk::class,
        'Page'        => Facades\Page::class,
        'Editor'      => Facades\Editor::class,
        'Tag'         => Facades\Tag::class,
        'Template'    => Facades\Template::class,
        'Group'       => Facades\Group::class,
        'Person'      => Facades\Person::class,
        'Router'      => Facades\Router::class,
        'Site'        => Facades\Site::class,
        'Str'         => Str::class,
    ];

    protected $serviceProviders = [
        ServiceProviders\TemplateServiceProvider::class,
        ServiceProviders\RouteServiceProvider::class,
        ServiceProviders\RepositoryServiceProvider::class,
        ServiceProviders\AuthServiceProvider::class,
        ServiceProviders\EditorServiceProvider::class,
        ServiceProviders\SettingsServiceProvider::class,
        ServiceProviders\ChunkServiceProvider::class,
        ServiceProviders\EventServiceProvider::class,
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
        include __DIR__.'/../../functions.php';

        $this->app->singleton(BoomCMS::class, function () {
            return new BoomCMS();
        });

        $this->registerAliases();
        $this->registerServiceProviders();

        $this->mergeConfigFrom(__DIR__.'/../../config/auth.php', 'auth');
        $this->mergeConfigFrom(__DIR__.'/../../config/mail.php', 'mail');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/menu.php', 'boomcms.menu');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/text_editor_toolbar.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/viewHelpers.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/settingsManagerOptions.php', 'boomcms');
        $this->mergeConfigFrom(__DIR__.'/../../config/boomcms/assets.php', 'boomcms');
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
