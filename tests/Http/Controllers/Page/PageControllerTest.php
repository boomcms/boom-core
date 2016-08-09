<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use BoomCMS\Events\PageWasReverted;
use BoomCMS\Http\Controllers\Page\PageController as Controller;
use BoomCMS\Jobs\CreatePage;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Facades\PageVersion as PageVersionFacade;
use Mockery as m;

class PageControllerTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testPostAdd()
    {
        $this->login();

        $site = new Site();
        $url = new URL([URL::ATTR_LOCATION => 'test']);
        $parent = m::mock(Page::class)->makePartial();

        $page = new Page();
        $page->{Page::ATTR_ID} = 1;

        $this->requireRole('add', $parent);

        $parent
            ->shouldReceive('getAddPageParent')
            ->once()
            ->andReturnSelf();

        $this->controller
            ->shouldReceive('dispatch')
            ->once()
            ->with(m::type(CreatePage::class))
            ->andReturn($page);

        PageFacade::shouldReceive('find')
            ->once()
            ->with($page->getId())
            ->andReturn($page);

        $this->assertEquals($page, $this->controller->postAdd($site, $parent));
    }

    public function testPostDiscard()
    {
        $page = new Page();

        $this->requireRole('edit', $page);

        $this->expectsEvents(PageWasReverted::class);

        PageVersionFacade::shouldReceive('deleteDrafts')
            ->once()
            ->with($page);

        $this->controller->postDiscard($page);
    }
}
