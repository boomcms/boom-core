<?php

namespace BoomCMS\Tests;

use BoomCMS\BoomCMS;
use Mockery as m;

class BoomCMSTest extends AbstractTestCase
{
    public function testGetVersion()
    {
        $boomcms = m::mock(BoomCMS::class)->makePartial();

        $this->assertEquals(BoomCMS::VERSION, $boomcms->getVersion());
    }
}
