<?php

namespace BoomCMS\Tests\ServiceProviders;

use BoomCMS\Repositories;
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
            'boomcms.repositories.asset'    => Repositories\Asset::class,
            'boomcms.repositories.group'    => Repositories\Group::class,
            'boomcms.repositories.page'     => Repositories\Page::class,
            'boomcms.repositories.person'   => Repositories\Person::class,
            'boomcms.repositories.tag'      => Repositories\Tag::class,
            'boomcms.repositories.template' => Repositories\Template::class,
            'boomcms.repositories.url'      => Repositories\URL::class,
        ];

        $app = m::mock(Application::class);

        foreach ($expectations as $key => $class) {
            $app
                ->shouldReceive('singleton')
                ->once()
                ->with($key, m::any());
        }

        $sp = new RepositoryServiceProvider($app);
        $sp->register();

        foreach ($expectations as $key => $class) {
            $this->assertInstanceOf($class, App::offsetGet($key));
        }
    }
}
