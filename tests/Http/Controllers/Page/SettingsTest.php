<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
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

    public function testPostVisiblityMakesPageInvisible()
    {
        $this->requireRole('editContent', $this->page);

        $request = new Request(['visible_from' => 0]);

        $this->page
            ->shouldReceive('setVisibleFrom')
            ->once()
            ->with(null)
            ->andReturnSelf();

        $this->page
            ->shouldReceive('setVisibleTo')
            ->once()
            ->with(null)
            ->andReturnSelf();

        PageFacade::shouldReceive('save')
            ->once()
            ->with($this->page);

        $this->assertEquals(0, $this->controller->postVisibility($request, $this->page));
    }
}
