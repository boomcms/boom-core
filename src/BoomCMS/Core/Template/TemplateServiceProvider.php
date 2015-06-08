<?php

namespace BoomCMS\Core\Template;

use Illuminate\Support\Facades\Filesystem;
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
        foreach($this->themes as $theme) {
            $views = $this->manager->getThemeDirectory($theme) . '/src/views';
            $public = $this->manager->getThemeDirectory($theme) . '/public';
            $routes = $this->manager->getThemeDirectory($theme) . '/routes.php';

            if ($this->app['filesystem']->exists($views)) {
                $this->loadViewsFrom($views, $theme);
            }

            if ($this->app['filesystem']->exists($public)) {
                $this->publishes([$public => public_path('vendor/boomcms/themes/' . $theme)], $theme);
            }

            if ($this->app['filesystem']->exists($routes)) {
                include $routes;
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

        $this->app->singleton('boomcms.template.provider', function ($app) use($provider) {
            return $provider;
        });

        $this->app->singleton('boomcms.template.manager', function ($app) use($manager) {
            return $manager;
        });

        $this->themes = $manager->findInstalledThemes();

        foreach($this->themes as $theme) {
            $config = $theme->getConfigDirectory() . DIRECTORY_SEPARATOR . 'themes.php';

            if ($this->app['filesystem']->exists($config)) {
                $this->mergeConfigFrom($config, 'boomcms');
            }
        }
    }
}
