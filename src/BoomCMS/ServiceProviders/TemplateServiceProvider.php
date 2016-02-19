<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Template\Manager as TemplateManager;
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
        $manager = new TemplateManager($this->app['files'], $this->app['boomcms.repositories.template'], $this->app['cache.store']);

        $this->app->singleton('boomcms.template.manager', function () use ($manager) {
            return $manager;
        });

        $this->themes = $manager->getInstalledThemes();

        foreach ($this->themes as $theme) {
            Config::merge($theme->getConfigDirectory().DIRECTORY_SEPARATOR.'boomcms.php');
        }

        foreach ($this->themes as $theme) {
            $views = $theme->getViewDirectory();
            $init = $theme->getDirectory().DIRECTORY_SEPARATOR.'init.php';

            $this->loadViewsFrom($views, $theme->getName());
            $this->loadViewsFrom($views.'/chunks', 'boomcms.chunks');

            if (file_exists($init)) {
                include $init;
            }
        }
    }

    public function register()
    {
    }
}
