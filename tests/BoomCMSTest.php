<?php

namespace BoomCMS\Tests;

use BoomCMS\BoomCMS;

class BoomCMSTest extends AbstractTestCase
{
    public function testGetVersion()
    {
        $boomcms = new BoomCMS();

        $this->assertEquals(BoomCMS::VERSION, $boomcms->getVersion());
    }
}
