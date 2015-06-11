<?php

use BoomCMS\Core\Asset\Asset;

class Asset_AssetTest extends TestCase
{
    public function testDirectory()
    {
        $this->assertEquals(storage_path() . '/boomcms/assets', Asset::directory());
    }
}