<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Page;
use BoomCMS\Routing\Router as BoomCMSRouter;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $models = [
        'Asset',
        'Group',
        'Page',
        'Person',
        'Site',
        'Tag',
        'Template',
        'URL',
    ];

    public function boot(Router $router)
    {
        foreach ($this->models as $model) {
            $binding = strtolower($model);
            $className = "BoomCMS\Database\Models\\$model";

            $router->model($binding, $className);
        }

        $router->model('related', Page::class);
    }

    public function register()
    {
        $this->app->singleton(BoomCMSRouter::class, function () {
            return new BoomCMSRouter($this->app);
        });

        require __DIR__.'/../../routes.php';
    }
}
