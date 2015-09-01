<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Template;
use BoomCMS\Support\Helpers\Config as ConfigHelper;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    protected $themes = [];

    /**
     * @var Template\Manager
     */
    protected $manager;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->themes = $this->manager->findInstalledThemes();

        foreach ($this->themes as $theme) {
            ConfigHelper::merge($theme->getConfigDirectory().DIRECTORY_SEPARATOR.'boomcms.php');
        }

        foreach ($this->themes as $theme) {
            $views = $theme->getViewDirectory();
            $public = $theme->getPublicDirectory();
            $init = $theme->getDirectory().DIRECTORY_SEPARATOR.'init.php';
            $migrations = $theme->getDirectory().'/migrations/';

            $this->loadViewsFrom($views, $theme->getName());
            $this->loadViewsFrom($views.'/chunks', 'boomcms.chunks');

            if (file_exists($public)) {
                $this->publishes([
                    $public => public_path('vendor/boomcms/themes/'.$theme),
                ], $theme->getName());
            }

            if (file_exists($migrations)) {
                $this->publishes([
                    $migrations => base_path('/migrations/boomcms'),
                ], $theme->getName());
            }

            if (file_exists($init)) {
                include $init;
            }

            $config = Config::get("boomcms.themes.$theme");

            // Register View shared variables for this theme.
            if (isset($config['shared'])) {
                foreach ($config['shared'] as $var => $value) {
                    View::share($var, $value);
                }
            }

            // Register View composers for this theme
            if (isset($config['composers'])) {
                foreach ($config['composers'] as $view => $composer) {
                    View::composer($view, $composer);
                }
            }

            // Register View creators for this theme
            if (isset($config['creators'])) {
                foreach ($config['creators'] as $view => $creator) {
                    View::creator($view, $creator);
                }
            }
        }
    }

    /**
     * @return void
     */
    public function register()
    {
        $provider = new Template\Provider();
        $this->manager = $manager = new Template\Manager($this->app['files'], $provider);

        $this->app->singleton('boomcms.template.provider', function ($app) use ($provider) {
            return $provider;
        });

        $this->app->singleton('boomcms.template.manager', function ($app) use ($manager) {
            return $manager;
        });
    }
}
