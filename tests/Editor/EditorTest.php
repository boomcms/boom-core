<?php

namespace BoomCMS\Tests\Editor;

use BoomCMS\Editor\Editor;
use BoomCMS\Tests\AbstractTestCase;
use DateTime;
use Illuminate\Support\Facades\Session;
use InvalidArgumentException;

class EditorTest extends AbstractTestCase
{
    public function testIsEnabledIfHasEditState()
    {
        $editor = $this->getEditor(['hasState']);

        $editor
            ->expects($this->once())
            ->method('hasState')
            ->with($this->equalTo(Editor::EDIT))
            ->will($this->returnValue(true));

        $this->assertTrue($editor->isEnabled());
    }

    public function testIsHistoryIsHasHistoryState()
    {
        $editor = $this->getEditor(['hasState']);

        $editor
            ->expects($this->once())
            ->method('hasState')
            ->with($this->equalTo(Editor::HISTORY))
            ->will($this->returnValue(true));

        $this->assertTrue($editor->isHistory());
    }

    public function testEnableIsAliasForSettingStateToEdit()
    {
        $editor = $this->getEditor(['setState']);

        $editor
            ->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Editor::EDIT));

        $editor->enable();
    }

    public function testEnableRemovesTheLiveTime()
    {
        $editor = $this->getEditor(['setTime']);

        $editor
            ->expects($this->once())
            ->method('setTime')
            ->with(null);

        $editor->enable();
    }

    public function testGetTimeReturnsCurrentTimeByDefault()
    {
        Session::forget('editor_time');

        $editor = new Editor();
        $time = $editor->getTime();

        $this->assertEquals(time(), $time->getTimestamp());
    }

    public function testGetTimeReturnsSavedTime()
    {
        $time = time() - 1000;
        Session::put('editor_time', $time);

        $editor = new Editor();
        $editorTime = $editor->getTime();

        $this->assertEquals($time, $editorTime->getTimestamp());
    }

    public function testPreviewIsAliasForSettingStateToPreview()
    {
        $editor = $this->getEditor(['setState']);

        $editor
            ->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Editor::PREVIEW));

        $editor->preview();
    }

    public function testDisableIsAliasForSettingStateToDisabled()
    {
        $editor = $this->getEditor(['setState']);

        $editor
            ->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Editor::DISABLED));

        $editor->disable();
    }

    public function testSetStatThrowsExceptionForInvalidStates()
    {
        $editor = new Editor();
        $invalidStates = [0, null, 4, 'invalid'];

        foreach ($invalidStates as $state) {
            $this->setExpectedException(InvalidArgumentException::class);
            $editor->setState($state);
        }
    }

    public function testSetTimeWithTime()
    {
        $editor = $this->getEditor(['setState']);
        $timestamp = time() - 1000;

        $editor
            ->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Editor::HISTORY));

        Session::shouldReceive('put')
            ->once()
            ->with('editor_time', $timestamp);

        $editor->setTime(new DateTime('@'.$timestamp));
    }

    public function testSetTimeNull()
    {
        $editor = $this->getEditor(['setState']);

        $editor
            ->expects($this->never())
            ->method('setState');

        Session::shouldReceive('put')
            ->once()
            ->with('editor_time', null);

        $editor->setTime(null);
    }

    protected function getEditor($methods = null)
    {
        return $this->getMockBuilder(Editor::class)
            ->setMethods($methods)
            ->getMock();
    }
}
