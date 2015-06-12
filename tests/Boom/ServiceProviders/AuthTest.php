<?php

use BoomCMS\Core\Auth\AuthServiceProvider;

class ServiceProviders_AuthTest extends TestCase
{
	public function testAuthServiceProvider()
	{
		$this->app['boomcms.person.provider'] = $this->getMockPersonProvider();
		$this->app['session'] = $this->getMockSession();
		$this->app['cookie'] = $this->getMockCookieJar();
		
		$serviceProvider = new AuthServiceProvider($this->app);
		$serviceProvider->boot();
		
		$this->assertInstanceOf('BoomCMS\COre\Auth\Auth', $this->app['boomcms.auth']);
	}
}