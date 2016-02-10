<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use BoomCMS\Events\PageWasCreated;
use BoomCMS\Http\Controllers\Page\PageController as Controller;
use BoomCMS\Jobs\CreatePage;
use BoomCMS\Support\Facades\PageVersion as PageVersionFacade;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Support\Facades\Event;
use Mockery as m;

class PageControllerTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testPostAdd()
    {
        auth()->login(new Person());

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

        URLFacade::shouldReceive('page')
            ->once()
            ->with($page)
            ->andReturn($url);

        Event::shouldReceive('fire')
            ->once()
            ->with(m::type(PageWasCreated::class));

        $expected = [
            'url' => (string) $url,
            'id'  => 1,
        ];

        $this->assertEquals($expected, $this->controller->postAdd($site, $parent));
    }

    public function testPostDiscard()
    {
        $page = new Page();

        $this->requireRole('edit', $page);

        PageVersionFacade::shouldReceive('deleteDrafts')
            ->once()
            ->with($page);

        $this->controller->postDiscard($page);
    }
}
