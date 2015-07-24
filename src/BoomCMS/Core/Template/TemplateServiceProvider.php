<?php

namespace BoomCMS\Core\Template;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    protected $themes = [];

    /**
     *
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
            $config = $theme->getConfigDirectory() . DIRECTORY_SEPARATOR . 'themes.php';

            file_exists($config) && $this->mergeConfigFrom($config, 'boomcms.themes');
        }

        foreach ($this->themes as $theme) {
            $views = $theme->getViewDirectory();
            $public = $theme->getPublicDirectory();
            $routes = $theme->getDirectory() . DIRECTORY_SEPARATOR . 'routes.php';

            $this->loadViewsFrom($views, $theme->getName());
            $this->loadViewsFrom($views . '/chunks', 'boomcms.chunks');

            $this->publishes([
                $public => public_path('vendor/boomcms/themes/' . $theme),
                $theme->getDirectory() . '/migrations/' => base_path('/migrations/boomcms'),
            ], $theme->getName());

            if (file_exists($routes)) {
                include $routes;
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
	 *
	 * @return void
	 */
    public function register()
    {
        $provider = new Provider();
        $this->manager = $manager = new Manager($this->app['files'], $provider, false);

        $this->app->singleton('boomcms.template.provider', function ($app) use ($provider) {
            return $provider;
        });

        $this->app->singleton('boomcms.template.manager', function ($app) use ($manager) {
            return $manager;
        });
    }
}
