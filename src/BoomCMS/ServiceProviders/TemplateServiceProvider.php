<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Core\Template\Manager as TemplateManager;
use BoomCMS\Database\Models\Template as TemplateModel;
use BoomCMS\Repositories\Template as TemplateRepository;
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
        $manager = new TemplateManager($this->app['files'], $this->app['boomcms.repositories.template']);

        $this->app->singleton('boomcms.template.manager', function ($app) use ($manager) {
            return $manager;
        });

        $this->themes = $manager->findInstalledThemes();

        foreach ($this->themes as $theme) {
            Config::merge($theme->getConfigDirectory().DIRECTORY_SEPARATOR.'boomcms.php');
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
        }
    }

    public function register()
    {
    }
}
