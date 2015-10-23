<?php

namespace BoomCMS\Tests;

use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Person\Person;
use BoomCMS\Tests\AbstractTestCase;

class EditorTest extends AbstractTestCase
{
    protected $auth, $session;

    public function setUp()
    {
        parent::setUp();

        $this->session = $this->getMockSession();
        $provider = $this->getMock('BoomCMS\Core\Person\Provider');
        $permissionsProvider = $this->getMock('BoomCMS\Core\Auth\PermissionsProvider');
        $this->auth = $this->getMock('BoomCMS\Core\Auth\Auth', ['getPerson', 'loggedIn'], [$this->session, $provider, $permissionsProvider]);

        $this->auth
            ->expects($this->any())
            ->method('getPerson')
            ->will($this->returnValue(new Person(['id' => 1])));
    }

    public function testSetGetActivePageAlwaysReturnsAPage()
    {
        $this->assertInstanceOf('BoomCMS\Core\Page\Page', $this->getEditor()->getActivePage());
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
        return $this->getMockBuilder('BoomCMS\Core\Editor\Editor')
            ->setMethods($methods)
            ->setConstructorArgs([$this->auth, $this->session])
            ->getMock();
    }
}
