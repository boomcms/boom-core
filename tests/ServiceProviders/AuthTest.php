<?php

namespace BoomCMS\Tests\ServiceProviders;

use BoomCMS\ServiceProviders\AuthServiceProvider;
use BoomCMS\Tests\AbstractTestCase;

class AuthTest extends AbstractTestCase
{
    public function testAuthServiceProvider()
    {
        $this->app['boomcms.person.provider'] = $this->getMockPersonRepository();
        $this->app['session'] = $this->getMockSession();

        $serviceProvider = new AuthServiceProvider($this->app);
        $serviceProvider->boot();

        $this->assertInstanceOf('BoomCMS\COre\Auth\Auth', $this->app['boomcms.auth']);
    }
}
