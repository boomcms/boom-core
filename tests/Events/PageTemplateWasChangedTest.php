<?php

namespace BoomCMS\Tests\Events;

use BoomCMS\Database\Models\Template;
use BoomCMS\Events\PageTemplateWasChanged;
use BoomCMS\Tests\AbstractTestCase;

class PageTemplateWasChangedTest extends AbstractTestCase
{
    public function testGetNewTemplate()
    {
        $template = new Template();
        $page = $this->validPage();
        $event = new PageTemplateWasChanged($page, $template);

        $this->assertEquals($template, $event->getNewTemplate());
    }
}