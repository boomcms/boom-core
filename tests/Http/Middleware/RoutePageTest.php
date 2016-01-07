<?php

namespace BoomCMS\Tests\Http\Middleware;

use BoomCMS\Http\Middleware\RoutePage;
use BoomCMS\Routing\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Iluminate\Routing\Route;
use Mockery as m;

class RoutePageTest extends AbstractTestCase
{
    public function testPageIsRouted()
    {
        $uri = '/test';

        $router = m::mock(Router::class);
        $router->shouldReceive('routePage')->with($uri);

        $route = m::mock(Route::class);
        $route->shouldReceive('getParameter')->with('location')->andReturn($uri);

        $request = m::mock(Request::class);
        $request->shouldReceive('route')->andReturn($route);

        $middleware = new RoutePage($router);
        $middleware->handle($request, function () {});
    }
}
