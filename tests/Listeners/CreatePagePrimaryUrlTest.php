<?php

namespace BoomCMS\Tests\Events;

use BoomCMS\Database\Models\Page;
use BoomCMS\Listeners\CreatePagePrimaryURL;
use BoomCMS\Tests\AbstractTestCase;

class CreatePagePrimaryUrlTest extends AbstractTestCase
{
    public function testGetPrefixIsEmptyForRootPage()
    {
        $listener = new CreatePagePrimaryURL();
        $page = new Page([Page::ATTR_PARENT => null]);

        $this->assertEquals('', $listener->getPrefix($page));
    }
}
