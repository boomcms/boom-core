<?php

namespace BoomCMS\Tests\ServiceProviders;

use BoomCMS\Repositories;
use BoomCMS\Routing\Router;
use BoomCMS\ServiceProviders\RepositoryServiceProvider;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\App;
use Mockery as m;

class RepositoryServiceProviderTest extends AbstractTestCase
{
    public function testRepositoriesAreRegistered()
    {
        $expectations = [
            Repositories\Asset::class,
            Repositories\Group::class,
            Repositories\Page::class,
            Repositories\PageVersion::class,
            Repositories\Person::class,
            Repositories\Tag::class,
            Repositories\Template::class,
            Repositories\URL::class,
        ];

        $app = m::mock(Application::class)->makePartial();

        foreach ($expectations as $class) {
            $app
                ->shouldReceive('singleton')
                ->once()
                ->with($class, m::any());
        }

        $sp = new RepositoryServiceProvider($app);
        $sp->boot($app->make(Router::class));

        foreach ($expectations as $class) {
            $this->assertInstanceOf($class, App::offsetGet($class));
        }
    }
}
