<?php

namespace BoomCMS\Tests\Routing;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use BoomCMS\Routing\Router;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Support\Facades\URL as URLFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Foundation\Application;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    protected $uri = '/test';

    public function setUp()
    {
        parent::setUp();

        $this->app = m::mock(Application::class)->makePartial();
        $this->router = m::mock(Router::class, [$this->app])->makePartial();
    }

    public function testSetAndGetActivePage()
    {
        $page = new Page();

        $this->app
            ->shouldReceive('instance')
            ->once()
            ->with(Page::class, $page);

        $this->router->setActivePage($page);

        $this->assertEquals($page, $this->router->getActivePage());
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

    public function testRoutePageByPrimaryUri()
    {
        $this->router->shouldReceive('getActiveSite')->andReturn(new Site());

        Editor::shouldReceive('isDisabled')
            ->once()
            ->andReturn(true);

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('isVisible')
            ->once()
            ->andReturn(true);

        $url = m::mock(URL::class);
        $url
            ->shouldReceive('getPage')
            ->once()
            ->andReturn($page);

        $url->shouldReceive('is')->andReturn(true);
        $page->shouldReceive('url')->andReturn($url);

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->with($this->router->getActiveSite(), $this->uri)
            ->once()
            ->andReturn($url);

        $this->router
            ->shouldReceive('setActivePage')
            ->once()
            ->with($page);

        $this->router->routePage($this->uri);
    }

    public function testRouteUrlDoesNotExist()
    {
        $this->setExpectedException(NotFoundHttpException::class);

        $this->router->shouldReceive('getActiveSite')->andReturn(new Site());

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->with($this->router->getActiveSite(), $this->uri)
            ->once()
            ->andReturnNull();

        $this->router->routePage($this->uri);
    }

    public function testRouteInvisiblePageIsNotFoundWhenEditorDisabled()
    {
        $this->setExpectedException(NotFoundHttpException::class);

        $this->router->shouldReceive('getActiveSite')->andReturn(new Site());

        Editor::shouldReceive('isDisabled')
            ->once()
            ->andReturn(true);

        $page = m::mock(Page::class);
        $page
            ->shouldReceive('isVisible')
            ->once()
            ->andReturn(false);

        $url = m::mock(URL::class);
        $url
            ->shouldReceive('getPage')
            ->once()
            ->andReturn($page);

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->with($this->router->getActiveSite(), $this->uri)
            ->once()
            ->andReturn($url);

        $this->router->routePage($this->uri);
    }

    public function testRouteInvisiblePageIsFoundWhenEditorEnabled()
    {
        $this->router->shouldReceive('getActiveSite')->andReturn(new Site());

        Editor::shouldReceive('isDisabled')
            ->once()
            ->andReturn(false);

        $page = m::mock(Page::class);

        $url = m::mock(URL::class);
        $url
            ->shouldReceive('getPage')
            ->once()
            ->andReturn($page);

        $url
            ->shouldReceive('is')
            ->andReturn(true);

        $page
            ->shouldReceive('url')
            ->andReturn($url);

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->with($this->router->getActiveSite(), $this->uri)
            ->once()
            ->andReturn($url);

        $this->router
            ->shouldReceive('setActivePage')
            ->once()
            ->with($page);

        $this->router->routePage($this->uri);
    }

    public function testRoutePageDeleted()
    {
        $this->setExpectedException(GoneHttpException::class);

        $this->router->shouldReceive('getActiveSite')->andReturn(new Site());

        $url = m::mock(URL::class);
        $url
            ->shouldReceive('getPage')
            ->once()
            ->andReturnNull();

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->with($this->router->getActiveSite(), $this->uri)
            ->once()
            ->andReturn($url);

        $this->router->routePage($this->uri);
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
