<?php

namespace BoomCMS\Tests\ServiceProviders;

use BoomCMS\ServiceProviders\PersonServiceProvider;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Foundation\Application;
use Mockery as m;

class PersonServiceProviderTest extends AbstractTestCase
{
    public function testRepositoriesAreRegistered()
    {
        $app = m::mock(Application::class);
        $app
            ->shouldReceive('singleton')
            ->with('boomcms.repositories.person', m::any());

        $app
            ->shouldReceive('singleton')
            ->with('boomcms.repositories.group', m::any());

        $sp = new PersonServiceProvider($app);
        $sp->register();
    }
}
