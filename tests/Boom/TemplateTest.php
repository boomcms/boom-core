<?php

use BoomCMS\Core\Template;
use BoomCMS\Core\Template\Manager;

class TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testLoadedIfHasId()
    {
        $template = new Template\Template(['id' => 2]);

        $this->assertTrue($template->loaded());
    }

    public function testNotLoadedIfNoId()
    {
        $template = new Template\Template([]);

        $this->assertFalse($template->loaded());
    }
}