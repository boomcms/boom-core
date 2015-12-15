<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(Router $router)
    {
        $router->model('asset', Models\Asset::class);
        $router->model('page', Models\Page::class);
        $router->model('group', Models\Group::class);
        $router->model('template', Models\Template::class);
        $router->model('url', Models\URL::class);

        require __DIR__.'/../../routes.php';
    }

    public function register()
    {
    }
}
