<?php

namespace BoomCMS\Tests\Models\Asset;

use BoomCMS\Database\Models\Asset\Version;
use BoomCMS\Tests\AbstractTestCase;

class VersionTest extends AbstractTestCase
{
    public function testExtensionIsAlwaysLowercase()
    {
        $v = new Version();
        $v->extension = 'TXT';

        $this->assertEquals('txt', $v->extension);
    }

    public function testExtensionIsOnlyAlphaNumeric()
    {
        $v = new Version();
        $v->extension = '. mp3 ';

        $this->assertEquals('mp3', $v->extension);
    }
}
