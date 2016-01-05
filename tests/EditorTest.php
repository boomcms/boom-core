<?php

namespace BoomCMS\Tests;

use BoomCMS\Editor\Editor;

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

    public function testEnableIsAliasForSettingStateToEdit()
    {
        $editor = $this->getEditor(['setState']);

        $editor
            ->expects($this->once())
            ->method('setState')
            ->with($this->equalTo(Editor::EDIT));

        $editor->enable();
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

    protected function getEditor($methods = null)
    {
        return $this->getMockBuilder(Editor::class)
            ->setMethods($methods)
            ->getMock();
    }
}
