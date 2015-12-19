<?php

namespace BoomCMS\Tests\ServiceProviders;

use BoomCMS\Database\Models;
use BoomCMS\ServiceProviders\RouteServiceProvider;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Routing\Router;
use Mockery as m;

class RouteServiceProviderTest extends AbstractTestCase
{
    public function testModelBidings()
    {
        $router = m::mock(Router::class);

        foreach ([
            Models\Group::class,
            Models\Asset::class,
            Models\Page::class,
            Models\Person::class,
            Models\Template::class,
            Models\URL::class,
        ] as $model) {
            $binding = strtolower(class_basename($model));

            $router->shouldReceive('model')->once()->with($binding, $model);
        }

        $sp = m::mock(RouteServiceProvider::class)->makePartial();
        $sp->boot($router);
    }
}
