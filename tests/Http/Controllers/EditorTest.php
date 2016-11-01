<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Editor\Editor as Editor;
use BoomCMS\Http\Controllers\Editor as EditorController;
use BoomCMS\Support\Facades\Page as PageFacade;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\View;
use Mockery as m;

class EditorTest extends BaseControllerTest
{
    protected $className = EditorController::class;

    public function testState()
    {
        $editor = m::mock(Editor::class);
        $states = [
            'edit'     => 1,
            'disabled' => 2,
            'preview'  => 3,
            'history'  => 4,
        ];

        foreach ($states as $state => $value) {
            $editor
                ->shouldReceive('setState')
                ->once()
                ->with($value);

            $request = new Request(['state' => $state]);

            $this->controller->postState($request, $editor);
        }
    }

    public function testPostTime()
    {
        $session = m::mock(Store::class)->makePartial();

        $timestamp = time() - 1000;
        $editor = m::mock(Editor::class, [$session])->makePartial();

        $editor
            ->shouldReceive('setTime')
            ->once()
            ->with(m::on(function (DateTime $time) use ($timestamp) {
                return $time->getTimestamp() === $timestamp;
            }))
            ->andReturnSelf();

        $request = new Request(['time' => $timestamp]);

        $this->controller->postTime($request, $editor);
    }

    public function testGetEditToolbar()
    {
        $pageId = 1;
        $view = 'view contents';
        $page = new Page();
        $request = new Request(['page_id' => $pageId]);

        PageFacade::shouldReceive('find')
            ->once()
            ->with($pageId)
            ->andReturn($page);

        $editor = m::mock(Editor::class)->makePartial();
        $editor->shouldReceive('isEnabled')->once()->andReturn(true);

        View::shouldReceive('share')
            ->once()
            ->with(m::subset(['page' => $page]));

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.toolbar.edit', m::any(), m::any())
            ->andReturn($view);

        $this->assertEquals($view, $this->controller->getToolbar($editor, $request));
    }

    public function testGetPreviewToolbar()
    {
        $pageId = 1;
        $view = 'view contents';
        $page = new Page();
        $request = new Request(['page_id' => $pageId]);

        PageFacade::shouldReceive('find')
            ->once()
            ->with($pageId)
            ->andReturn($page);

        $editor = m::mock(Editor::class)->makePartial();
        $editor->shouldReceive('isEnabled')->once()->andReturn(false);

        View::shouldReceive('share')
            ->once()
            ->with(m::subset(['page' => $page]));

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.toolbar.preview', m::any(), m::any())
            ->andReturn($view);

        $this->assertEquals($view, $this->controller->getToolbar($editor, $request));
    }
}
