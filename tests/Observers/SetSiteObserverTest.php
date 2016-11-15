<?php

namespace BoomCMS\Tests\Observers;

use BoomCMS\Contracts\SingleSiteInterface;
use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Routing\Router;
use BoomCMS\Support\Facades\Site as SiteFacade;
use BoomCMS\Observers\SetSiteObserver;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\App;
use Mockery as m;

class SetSiteObserverTest extends AbstractTestCase
{
    /**
     * @var Router
     */
    protected $router;

    public function setUp()
    {
        parent::setUp();

        $this->router = new Router(App::getFacadeRoot());
    }

    public function testActiveSiteIsSet()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $this->router->setActiveSite($site);

        $page = new Page();

        $observer = new SetSiteObserver($this->router, SiteFacade::getFacadeRoot());
        $observer->creating($page);

        $this->assertEquals($site->getId(), $page->{SingleSiteInterface::ATTR_SITE});
    }

    public function testSetDefaultSiteIsSet()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;

        SiteFacade::shouldReceive('findDefault')->once()->andReturn($site);

        $page = new Page();

        $observer = new SetSiteObserver($this->router, SiteFacade::getFacadeRoot());
        $observer->creating($page);

        $this->assertEquals($site->getId(), $page->{SingleSiteInterface::ATTR_SITE});
    }

    public function testNothingIsDoneForModelWhichIsntMultiSite()
    {
        $person = m::mock(Person::class);
        $person->shouldReceive('setAttribute')->never();

        SiteFacade::shouldReceive('findDefault')->never();

        $observer = new SetSiteObserver($this->router, SiteFacade::getFacadeRoot());
        $observer->creating($person);
    }
}
