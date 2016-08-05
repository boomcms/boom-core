<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Http\Controllers\Page\Settings as Controller;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Http\Request;
use Mockery as m;

class SettingsTest extends BaseControllerTest
{
    protected $className = Controller::class;

    /**
     * @var Page
     */
    protected $page;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class)->makePartial();
    }

    public function testGetInfo()
    {        $this->page
            ->shouldReceive('getCreatedBy')
            ->andReturn(new Person());

        $view = view('boomcms::editor.page.settings.info', ['page' => $this->page]);

        $this->assertEquals($view->render(), $this->controller->getInfo($this->page)->render());
    }

    public function testGetInfoWhenCreatedByIsNull()
    {
        $this->page
            ->shouldReceive('getCreatedBy')
            ->andReturnNull();

        $view = view('boomcms::editor.page.settings.info', ['page' => $this->page]);

        $this->assertEquals($view->render(), $this->controller->getInfo($this->page)->rendeR());
    }

    public function testPostVisiblityMakesPageInvisible()
    {
        $this->requireRole('publish', $this->page);

        $request = new Request(['visible' => 0]);

        PageFacade::shouldReceive('save')
            ->once()
            ->with($this->page);

        $this->assertEquals(0, $this->controller->postVisibility($request, $this->page));
    }
}
