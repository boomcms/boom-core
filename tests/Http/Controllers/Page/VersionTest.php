<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Database\Models\Template;
use BoomCMS\Http\Controllers\Page\Version as Controller;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Tests\Http\Controllers\BaseControllerTest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mockery as m;

class VersionTest extends BaseControllerTest
{
    protected $className = Controller::class;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var PageVersion
     */
    protected $version;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class)->makePartial();
        $this->version = m::mock(PageVersion::class)->makePartial();

        $this->page
            ->shouldReceive('getCurrentVersion')
            ->andReturn($this->version);
    }

    public function testGetEmbargo()
    {
        $this->requireRole('publish', $this->page);

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.page.version.embargo', [
                'version' => $this->version
            ], [])
            ->andReturn('view');

        $this->assertEquals('view', $this->controller->getEmbargo($this->page));
    }

    public function testGetStatus()
    {
        $this->requireRole('editContent', $this->page);

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.page.version.status', [
                'page'    => $this->page,
                'version' => $this->version,
                'auth'    => auth(),
            ], [])
            ->andReturn('view');

        $this->assertEquals('view', $this->controller->getStatus($this->page));
    }

    public function testGetTemplate()
    {
        $this->requireRole('editTemplate', $this->page);

        $template = new Template();

        $this->page
            ->shouldReceive('getTemplate')
            ->once()
            ->andReturn($template);

        TemplateFacade::shouldReceive('findValid')->once()->andReturn([]);

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.page.version.template', [
                'current'   => $template,
                'templates' => [],
            ], [])
            ->andReturn('view');

        $this->assertEquals('view', $this->controller->getTemplate($this->page));
    }

    public function testRequestApproval()
    {
        $this->requireRole('editContent', $this->page);

        $status = 'pending approval';

        $this->page
            ->shouldReceive('markUpdatesAsPendingApproval')
            ->once()
            ->andReturnSelf();

        $this->version
            ->shouldReceive('getStatus')
            ->once()
            ->andReturn($status);

        $this->assertEquals($status, $this->controller->requestApproval($this->page));
    }

    public function testSetEmabargoTime()
    {
        $this->markTestIncomplete();
    }
}
