<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\Url;
use BoomCMS\Http\Controllers\Page\Urls as Controller;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Http\Request;
use Mockery as m;

class UrlsTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    /**
     * @var Site
     */
    protected $site;

    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class)->makePartial();
        $this->site = m::mock(Site::class)->makePartial();

        $this->page->{Page::ATTR_ID} = 1;
    }

    public function testGetAdd()
    {
        $response = view('boomcms::editor.urls.add', ['page' => $this->page])->render();

        $this->assertEquals($response, $this->controller->getAdd($this->page));
    }

    public function testPostAddUrlIsUnique()
    {
        $location = 'test';
        $request = new Request(['location' => $location]);

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->once()
            ->with($this->site, $location)
            ->andReturnNull();

        URLFacade::shouldReceive('create')
            ->once()
            ->with($location, $this->page);

        $this->controller->postAdd($request, $this->site, $this->page);
    }

    public function testPostAddUrlIsInUse()
    {
        $location = 'test';
        $request = new Request(['location' => $location]);

        $url = m::mock(URL::class)->makePartial();
        $url->{URL::ATTR_ID} = 2;

        $url
            ->shouldReceive('isForPage')
            ->once()
            ->with($this->page)
            ->andReturn(false);

        URLFacade::shouldReceive('findBySiteAndLocation')
            ->once()
            ->with($this->site, $location)
            ->andReturn($url);

        URLFacade::shouldReceive('create')->never();

        $expected = ['existing_url_id' => $url->getId()];

        $this->assertEquals($expected, $this->controller->postAdd($request, $this->site, $this->page));
    }
}
