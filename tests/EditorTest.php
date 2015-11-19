<?php

namespace BoomCMS\Tests;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Auth\PermissionsProvider;
use BoomCMS\Editor\Editor;
use BoomCMS\Core\Page\Page;
use BoomCMS\Database\Models\Person;

class EditorTest extends AbstractTestCase
{
    protected $auth, $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = $this->getMockSession();
        $repository = $this->getMockPersonRepository();
        $permissionsProvider = $this->getMock(PermissionsProvider::class);
        $this->auth = $this->getMock(Auth::class, ['getPerson', 'loggedIn'], [$this->session, $repository, $permissionsProvider]);

        $this->auth
            ->expects($this->any())
            ->method('getPerson')
            ->will($this->returnValue(new Person(['id' => 1])));
    }

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
        $this->auth
            ->expects($this->once())
            ->method('loggedIn')
            ->will($this->returnValue(false));

        $editor = $this->getEditor();
        $editor->setActivePage(new Page(['id' => 1]));

        $this->assertFalse($editor->isActive());
    }

    public function testIsActiveIsFalseIfNoActivePageSet()
    {
        $editor = $this->getEditor();

        $this->assertFalse($editor->isActive());
    }

    public function testIsActiveIsTrueIfActivePageSetAndLoggedInUserCanEditPage()
    {
        $page = new Page(['id' => 1]);
        $this->auth
            ->expects($this->once())
            ->method('loggedIn')
            ->with($this->equalTo('edit_page'), $this->equalTo($page))
            ->will($this->returnValue(true));

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
        return $this->getMockBuilder(BoomCMS\Editor\Editor::class)
            ->setMethods($methods)
            ->setConstructorArgs([$this->auth, $this->session])
            ->getMock();
    }
}
