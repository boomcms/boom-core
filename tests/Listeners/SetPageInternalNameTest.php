<?php

namespace BoomCMS\Tests\Events;

use BoomCMS\Events\PageTitleWasChanged;
use BoomCMS\Listeners\SetPageInternalName;
use BoomCMS\Tests\AbstractTestCase;
use BoomCMS\Support\Facades\Page;

class SetPageInternalNameTest extends AbstractTestCase
{
    public function testInternalNameIsSet()
    {
        $page = $this->validPage();
        $title = 'test';
        $event = new PageTitleWasChanged($page, '', $title);

        Page::shouldReceive('internalNameExists')
            ->once()
            ->with($title)
            ->andReturn(false);

        Page::shouldReceive('save')
            ->once()
            ->with($page);

        $listener = new SetPageInternalName();
        $listener->handle($event);

        $this->assertEquals('test', $page->getInternalName());
    }

    public function testExistingInternalNameIsNotReplaced()
    {
        $page = $this->validPage();
        $title = 'test';
        $name = 'old-internal-name';

        $page->setInternalName($name);
        $event = new PageTitleWasChanged($page, '', $title);

        Page::shouldReceive('internalNameExists')->never();
        Page::shouldReceive('save')->never();

        $listener = new SetPageInternalName();
        $listener->handle($event);

        $this->assertEquals($name, $page->getInternalName());
    }
}
