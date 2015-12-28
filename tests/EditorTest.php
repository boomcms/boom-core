<?php

namespace BoomCMS\Tests;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Editor\Editor;
use Illuminate\Support\Facades\Auth;

class EditorTest extends AbstractTestCase
{
    public function testGetActivePageAlwaysReturnsAPage()
    {
        $this->assertInstanceOf(Page::class, $this->getEditor()->getActivePage());
    }

    public function testSetGetActivePage()
    {
        $editor = $this->getEditor();
        $page = new Page();

        $editor->setActivePage($page);
        $this->assertEquals($page, $editor->getActivePage());
    }

    public function testIsActiveIsFalseIfNotLoggedIn()
    {
        $page = $this->validPage();

        Auth::shouldReceive('check')
            ->once()
            ->withNoArgs()
            ->andReturn(false);

        $editor = $this->getEditor();
        $editor->setActivePage($page);

        $this->assertFalse($editor->isActive());
    }

    public function testIsActiveIsFalseIfNoActivePageSet()
    {
        $editor = $this->getEditor();

        $this->assertFalse($editor->isActive());
    }

    public function testIsActiveIsTrueIfActivePageSetAndLoggedInUserCanEditPage()
    {
        $page = $this->validPage();

        Auth::shouldReceive('check')
            ->once()
            ->withNoArgs()
            ->andReturn(true);

        Auth::shouldReceive('check')
            ->once()
            ->with('edit_page', $page)
            ->andReturn(true);

        $editor = $this->getEditor();
        $editor->setActivePage($page);

        $this->assertTrue($editor->isActive());
    }

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
