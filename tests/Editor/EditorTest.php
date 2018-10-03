<?php

namespace BoomCMS\Tests\Editor;

use BoomCMS\Editor\Editor;
use BoomCMS\Tests\AbstractTestCase;
use DateTime;
use Illuminate\Session\Store;
use InvalidArgumentException;
use Mockery as m;

class EditorTest extends AbstractTestCase
{
    /**
     * @var Store
     */
    protected $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = m::mock(Store::class)->makePartial();
    }

    public function testIsEnabledIfHasEditState()
    {
        $editor = $this->getEditor();

        $editor
            ->shouldReceive('hasState')
            ->once()
            ->with(Editor::EDIT)
            ->andReturn(true);

        $this->assertTrue($editor->isEnabled());
    }

    public function testIsHistoryIfHasHistoryState()
    {
        $editor = $this->getEditor();

        $editor
            ->shouldReceive('hasState')
            ->once()
            ->with(Editor::HISTORY)
            ->andReturn(true);

        $this->assertTrue($editor->isHistory());
    }

    public function testEnableIsAliasForSettingStateToEdit()
    {
        $editor = $this->getEditor();

        $editor
            ->shouldReceive('setState')
            ->once()
            ->with(Editor::EDIT);

        $editor->enable();
    }

    public function testGetTimeReturnsCurrentTimeByDefault()
    {
        $editor = $this->getEditor();
        $editor->setState(Editor::HISTORY);

        $this->session->forget('editor_time');

        $this->assertEquals(time(), $editor->getTime()->getTimestamp());
    }

    public function testGetTimeReturnsSavedTime()
    {
        $time = time() - 1000;
        $this->session->put('editor_time', $time);

        $editor = $this->getEditor();
        $editor->setState(Editor::HISTORY);

        $this->assertEquals($time, $editor->getTime()->getTimestamp());
    }

    public function testGetTimeReturnsCurrentTimeIfNotInHistoryMode()
    {
        $editor = $this->getEditor();

        foreach ([Editor::DISABLED, Editor::EDIT, Editor::PREVIEW] as $state) {
            $editor->setState($state);

            $this->assertEquals(time(), $editor->getTime()->getTimestamp());
        }
    }

    public function testPreviewIsAliasForSettingStateToPreview()
    {
        $editor = $this->getEditor();

        $editor
            ->shouldReceive('setState')
            ->once()
            ->with(Editor::PREVIEW);

        $editor->preview();
    }

    public function testDisableIsAliasForSettingStateToDisabled()
    {
        $editor = $this->getEditor();

        $editor
            ->shouldReceive('setState')
            ->once()
            ->with(Editor::DISABLED);

        $editor->disable();
    }

    public function testSetStatThrowsExceptionForInvalidStates()
    {
        $editor = $this->getEditor();
        $invalidStates = [0, null, 5, 'invalid'];

        foreach ($invalidStates as $state) {
            $this->expectException(InvalidArgumentException::class);
            $editor->setState($state);
        }
    }

    public function testSetTimeWithTime()
    {
        $editor = $this->getEditor();
        $timestamp = time() - 1000;

        $editor
            ->shouldReceive('setState')
            ->once()
            ->with(Editor::HISTORY);

        $this->session->shouldReceive('put')
            ->once()
            ->with('editor_time', $timestamp);

        $editor->setTime(new DateTime('@'.$timestamp));
    }

    public function testSetTimeNull()
    {
        $editor = $this->getEditor();

        $editor
            ->shouldReceive('setState')
            ->never();

        $this->session->shouldReceive('put')
            ->once()
            ->with('editor_time', null);

        $editor->setTime(null);
    }

    protected function getEditor()
    {
        return m::mock(Editor::class, [$this->session])->makePartial();
    }
}
