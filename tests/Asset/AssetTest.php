<?php

namespace BoomCMS\Tests\Asset;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Tests\AbstractTestCase;

class AssetTest extends AbstractTestCase
{
    public function testDirectory()
    {
        $this->assertEquals(storage_path().'/boomcms/assets', Asset::directory());
    }

    public function testGetFilename()
    {
        $asset = $this->getAsset(['version:id' => 1], ['getType']);

        $this->assertEquals(Asset::directory().'/1', $asset->getFilename());
    }

    public function testGetExtension()
    {
        $asset = $this->getAsset(['extension' => 'txt']);
        $this->assertEquals('txt', $asset->getExtension());

        $asset = $this->getAsset();
        $this->assertNull($asset->getExtension());
    }

    protected function getAsset($attrs = [], $methods = null)
    {
        return $this->getMockBuilder(Asset::class)
            ->setConstructorArgs([$attrs])
            ->setMethods($methods)
            ->getMock();
    }
}
