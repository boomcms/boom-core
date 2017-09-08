<?php

namespace BoomCMS\Theme;

use BoomCMS\Repositories\Template;
use BoomCMS\Support\Helpers\Config;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    protected $themes = [];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $manager = new ThemeManager($this->app['files'], $this->app[Template::class], $this->app['cache.store']);

        $this->app->singleton('boomcms.template.manager', function () use ($manager) {
            return $manager;
        });

        $this->themes = $manager->getInstalledThemes();

        foreach ($this->themes as $theme) {
            // Merge the configuration in the theme's src/config/boomcms.php file
            Config::merge($theme->getConfigDirectory().DIRECTORY_SEPARATOR.'boomcms.php');
        }

        foreach ($this->themes as $theme) {
            $this->loadViewsFromTheme($theme);
            $theme->init();
        }
    }

    /**
     * @see https://laravel.com/docs/5.4/packages#views
     *
     * @param Theme $theme
     */
    protected function loadViewsFromTheme(Theme $theme)
    {
        $views = $theme->getViewDirectory();

        $this->loadViewsFrom($views, $theme->getName());
        $this->loadViewsFrom($views.'/boomcms', 'boomcms');
        $this->loadViewsFrom($views.'/chunks', 'boomcms.chunks');
    }

    public function register()
    {
    }
}
