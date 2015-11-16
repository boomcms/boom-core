<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Repositories\Template as TemplateRepository;
use BoomCMS\Support\Helpers\Config;
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

    /**
     * @return void
     */
    public function register()
    {
        $repository = new TemplateRepository();
        $this->manager = $manager = new Template\Manager($this->app['files'], $repository);

        $this->app->singleton('boomcms.repositories.template', function ($app) use ($repository) {
            return $repository;
        });

        $this->app->singleton('boomcms.template.manager', function ($app) use ($manager) {
            return $manager;
        });
    }
}
