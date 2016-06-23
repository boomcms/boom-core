<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Database\Models\Template;
use BoomCMS\Database\Models\URL;
use BoomCMS\Http\Controllers\Page\Version as Controller;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use BoomCMS\Support\Facades\URL as URLFacade;
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
                'version' => $this->version,
            ], [])
            ->andReturn('view');

        $this->assertEquals('view', $this->controller->getEmbargo($this->page));
    }

    public function testGetStatus()
    {
        $this->requireRole('edit', $this->page);

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
        $this->requireRole('edit', $this->page);

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

    public function testPostEmbargoTime()
    {
        $this->requireRole('publish', $this->page);

        $now = time();
        $request = new Request(['embargo_until' => $now]);

        $this->page
            ->shouldReceive('setEmbargoTime')
            ->once()
            ->with(m::on(function (\DateTime $value) use ($now) {
                return $value->getTimestamp() === $now;
            }));

        $this->version
            ->shouldReceive('getStatus')
            ->andReturn('published');

        $this->assertEquals('published', $this->controller->postEmbargo($request, $this->page));
    }

    public function testPostTemplate()
    {
        $this->requireRole('editTemplate', $this->page);

        $template = new Template();
        $status = 'draft';

        $this->page
            ->shouldReceive('setTemplate')
            ->once()
            ->with($template);

        $this->version
            ->shouldReceive('getStatus')
            ->once()
            ->andReturn($status);

        $this->assertEquals($status, $this->controller->postTemplate($this->page, $template));
    }

    public function testPostTitle()
    {
        $this->requireRole('edit', $this->page);

        $title = 'test';
        $status = 'draft';
        $request = new Request(['title' => $title]);

        $url = new URL();
        $url->{URL::ATTR_LOCATION} = '/';

        $this->page
            ->shouldReceive('setTitle')
            ->once()
            ->with($title)
            ->andReturnSelf();

        $this->page
            ->shouldReceive('getTitle')
            ->andReturn($title);

        $this->version
            ->shouldReceive('getStatus')
            ->once()
            ->andReturn($status);

        PageFacade::shouldReceive('internalNameExists')
            ->once()
            ->with('test')
            ->andReturn(false);

        PageFacade::shouldReceive('save')->once()->with($this->page);

        URLFacade::shouldReceive('page')
            ->once()
            ->with($this->page)
            ->andReturn(new URL([URL::ATTR_LOCATION => '/']));

        $response = $this->controller->postTitle($request, $this->page);

        $this->assertEquals([
            'status'   => $status,
            'location' => 'http://localhost/',
        ], $response);

        $this->assertEquals($title, $this->page->getInternalName());
    }
}
