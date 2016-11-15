<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\Url;
use BoomCMS\Http\Controllers\Page\Urls as Controller;
use BoomCMS\Jobs\MakeURLPrimary;
use BoomCMS\Jobs\ReassignURL;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
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

    /**
     * @var URL
     */
    protected $url;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class)->makePartial();
        $this->site = m::mock(Site::class)->makePartial();
        $this->url = m::mock(URL::class)->makePartial();

        $this->page->{Page::ATTR_ID} = 1;
        $this->url->{URL::ATTR_ID} = 2;

        $this->requireRole('editUrls', $this->page);
    }

    public function testPostAddUrlIsUnique()
    {
        $location = 'test';
        $request = new Request(['location' => $location]);

        URLFacade::shouldReceive('findByLocation')
            ->once()
            ->with($location)
            ->andReturnNull();

        URLFacade::shouldReceive('create')
            ->once()
            ->with($location, $this->page);

        $this->controller->store($request, $this->page);
    }

    public function testPostAddUrlIsInUse()
    {
        $location = 'test';
        $request = new Request(['location' => $location]);

        $this->url
            ->shouldReceive('isForPage')
            ->once()
            ->with($this->page)
            ->andReturn(false);

        URLFacade::shouldReceive('findByLocation')
            ->once()
            ->with($location)
            ->andReturn($this->url);

        URLFacade::shouldReceive('create')->never();

        $expected = ['existing_url_id' => $this->url->getId()];

        $this->assertEquals($expected, $this->controller->store($request, $this->page));
    }

    public function testNonPrimaryUrlIsDeleted()
    {
        $this->url
            ->shouldReceive('isPrimary')
            ->once()
            ->andReturn(false);

        URLFacade::shouldReceive('delete')->once()->with($this->url);

        $this->controller->destroy($this->page, $this->url);
    }

    public function testPrimaryUrlIsNotDeleted()
    {
        $this->url
            ->shouldReceive('isPrimary')
            ->once()
            ->andReturn(true);

        URLFacade::shouldReceive('delete')->never();

        $this->controller->destroy($this->page, $this->url);
    }

    public function testMakePrimary()
    {
        Bus::shouldReceive('dispatch')
            ->once()
            ->with(m::type(MakeURLPrimary::class));

        $this->controller->makePrimary($this->page, $this->url);
    }

    public function testPostMove()
    {
        Bus::shouldReceive('dispatch')
            ->once()
            ->with(m::type(ReassignURL::class));

        $this->controller->postMove($this->page, $this->url);
    }
}
