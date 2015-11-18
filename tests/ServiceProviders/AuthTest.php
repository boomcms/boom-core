<?php

namespace BoomCMS\Tests\ServiceProviders;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\ServiceProviders\AuthServiceProvider;
use BoomCMS\Tests\AbstractTestCase;

class AuthTest extends AbstractTestCase
{
    public function testAuthServiceProvider()
    {
        $this->app['boomcms.repositories.person'] = $this->getMockPersonRepository();
        $this->app['session'] = $this->getMockSession();

        $serviceProvider = new AuthServiceProvider($this->app);
        $serviceProvider->boot();

        $this->assertInstanceOf(Auth::class, $this->app['boomcms.auth']);
    }
}
