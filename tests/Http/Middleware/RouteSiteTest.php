<?php

namespace BoomCMS\Tests\Http\Middleware;

use BoomCMS\Foundation\Http\Kernel;
use BoomCMS\Http\Middleware\RouteSite;
use BoomCMS\Routing\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery as m;

class RouteSiteTest extends AbstractTestCase
{
    public function testMiddlewareIsGloballyDefined()
    {
        $kernel = m::mock(Kernel::class)->makePartial();

        $this->assertTrue($kernel->hasMiddleware(RouteSite::class));
    }

    public function testSiteIsRouted()
    {
        $hostname = 'test.com';

        $router = m::mock(Router::class);
        $router->shouldReceive('routeHostname')->with($hostname);

        $request = m::mock(Request::class);
        $request->shouldReceive('getHttpHost')->andReturn($hostname);

        $middleware = new RouteSite($router);
        $middleware->handle($request, function () {});
    }
}
