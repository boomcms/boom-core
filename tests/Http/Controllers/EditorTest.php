<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Editor\Editor as Editor;
use BoomCMS\Http\Controllers\Editor as EditorController;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Mockery as m;

class EditorTest extends AbstractTestCase
{
    protected $controller;

    public function setUp()
    {
        parent::setUp();

        $this->controller = m::mock(EditorController::class)->makePartial();
    }

    public function testState()
    {
        $editor = m::mock(Editor::class);
        $states = [
            'edit'     => 1,
            'disabled' => 2,
            'preview'  => 3,
        ];

        foreach ($states as $state => $value) {
            $editor
                ->shouldReceive('setState')
                ->once()
                ->with($value);

            $request = new Request(['state' => $state]);

            $this->controller->setState($request, $editor);
        }
    }

    public function testGetEditToolbar()
    {
        $view = 'view contents';
        $page = new Page();

        $editor = m::mock(Editor::class);
        $editor->shouldReceive('isEnabled')->once()->andReturn(true);

        View::shouldReceive('share')
            ->once()
            ->with(m::subset(['page' => $page]));

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.toolbar', m::any(), m::any())
            ->andReturn($view);

        $this->assertEquals($view, $this->controller->getToolbar($editor, $page));
    }

    public function testGetPreviewToolbar()
    {
        $view = 'view contents';
        $page = new Page();

        $editor = m::mock(Editor::class);
        $editor->shouldReceive('isEnabled')->once()->andReturn(false);

        View::shouldReceive('share')
            ->once()
            ->with(m::subset(['page' => $page]));

        View::shouldReceive('make')
            ->once()
            ->with('boomcms::editor.toolbar_preview', m::any(), m::any())
            ->andReturn($view);

        $this->assertEquals($view, $this->controller->getToolbar($editor, $page));
    }
}
