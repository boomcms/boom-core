<?php

use BoomCMS\Core\Page\Page;

class Page_PageTest extends TestCase
{
    public function testGetParentReturnsPageObject()
    {
        $page = new Page([]);

        $this->assertInstanceOf('BoomCMS\Core\Page\Page', $page->getParent());
    }

    public function testGetTemplateId()
    {
        $page = new Page(['template_id' => 1]);

        $this->assertEquals(1, $page->getTemplateId());
    }
}