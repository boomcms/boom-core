<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models\Page;
use BoomCMS\Routing\Router as BoomCMSRouter;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
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

        require __DIR__.'/../../routes.php';
    }

    public function register()
    {
        $this->app->singleton(BoomCMSRouter::class, function () {
            $router = new BoomCMSRouter($this->app);

            if ($this->app->runningInConsole()) {
                return $router;
            }

            try {
                $router->routeHostname($this->app->make(Request::class)->getHttpHost());
            } catch (QueryException $e) {}

            return $router;
        });
    }
}
