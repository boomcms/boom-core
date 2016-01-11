<?php

namespace BoomCMS\Tests\Routing;

use BoomCMS\Database\Models\Site;
use BoomCMS\Routing\Router;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Foundation\Application;
use Mockery as m;

class RouterTest extends AbstractTestCase
{
    /**
     * @var Application
     */
    protected $app;

    protected $hostname = 'test.com';

    /**
     * @var Router
     */
    protected $router;

    public function setUp()
    {
        parent::setUp();

        $this->app = m::mock(Application::class)->makePartial();
        $this->router = m::mock(Router::class, [$this->app])->makePartial();
    }

    public function testGetActivePage()
    {
        $this->markTestIncomplete();
    }

    public function testSetAndGetActiveSite()
    {
        $site = new Site();

        $this->app
            ->shouldReceive('instance')
            ->once()
            ->with(Site::class, $site);

        $this->router->setActiveSite($site);

        $this->assertEquals($site, $this->router->getActiveSite());
    }

    public function testRouteHostname()
    {
        $site = new Site();

        SiteFacade::shouldReceive('findByHostname')
            ->once()
            ->with($this->hostname)
            ->andReturn($site);

        $this->router
            ->shouldReceive('setActiveSite')
            ->with($site);

        $this->router->routeHostname($this->hostname);
    }

    public function testRouteHostnameIsOverridenByEnvVar()
    {
        $envHostname = 'staging.test.com';
        $site = new Site();

        putenv("BOOMCMS_HOST=$envHostname");

        SiteFacade::shouldReceive('findByHostname')
            ->once()
            ->with($envHostname)
            ->andReturn($site);

        $this->router
            ->shouldReceive('setActiveSite')
            ->once()
            ->with($site);

        $this->router->routeHostname($this->hostname);

        putenv('BOOMCMS_HOST');
    }

    public function testRouteHostnameGetsDefaultSiteIfHostnameDoesntMatch()
    {
        $site = new Site();

        SiteFacade::shouldReceive('findByHostname')
            ->once()
            ->with($this->hostname)
            ->andReturn(null);

        SiteFacade::shouldReceive('findDefault')->andReturn($site);

        $this->router
            ->shouldReceive('setActiveSite')
            ->once()
            ->with($site);

        $this->router->routeHostname($this->hostname);
    }
}
