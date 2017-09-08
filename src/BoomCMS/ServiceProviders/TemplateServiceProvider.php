<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Theme\Theme;
use BoomCMS\Theme\ThemeManager;
use BoomCMS\Repositories\Template;
use BoomCMS\Support\Helpers\Config;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
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
     * Register the theme's views directory to load views from
     *
     * The main views directory is registered with the theme name as the namespace
     * This ensures that multiple themes can define views with the same filename
     *
     * Views in the views/boomcms are registered to the boomcms namespace
     * This allows themes to override boomcms views (e.g. to replace with BoomCMS login page with a branded login page)
     *
     * Views in the views/chunks directory are registered to the boomcms.chunks namespace
     * This namespace is checked by the chunk provider for chunk views.
     *
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
