<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;

class AssetTest extends AbstractModelTestCase
{
    protected $model = Asset::class;

    public function testDirectory()
    {
        $model = new Asset();

        $this->assertEquals(storage_path().'/boomcms/assets', $model->directory());
    }

    public function testGetFilename()
    {
        $asset = $this->getMock(Asset::class, ['getLatestVersionId']);
        $asset->expects($this->once())
            ->method('getLatestVersionId')
            ->will($this->returnValue(1));

        $this->assertEquals($asset->directory().'/1', $asset->getFilename());
    }

    public function testGetExtension()
    {
        $asset = $this->mockVersionedAttribute(['extension' => 'txt']);
        $this->assertEquals('txt', $asset->getExtension());

        $asset = $this->mockVersionedAttribute(['extension' => '']);
        $this->assertEquals('', $asset->getExtension());
    }

    public function testGetType()
    {
        $asset = $this->getAsset(['type' => 'image']);
        $this->assertEquals('image', $asset->getType());

        $asset = $this->getAsset(['type' => 'video']);
        $this->assertEquals('video', $asset->getType());

        $asset = $this->getAsset();
        $this->assertEquals('', $asset->getType());
    }

    public function testIsImage()
    {
        $image = $this->getAsset(['type' => 'image']);
        $this->assertTrue($image->isImage());

        $notAnImage = $this->getAsset(['type' => 'video']);
        $this->assertFalse($notAnImage->isImage());

        $empty = $this->getAsset();
        $this->assertFalse($empty->isImage());
    }

    public function testGetWidth()
    {
        $asset = $this->mockVersionedAttribute(['width' => 1]);
        $this->assertEquals(1, $asset->getWidth());
        $this->assertInternalType('int', $asset->getWidth());
    }

    public function testGetHeight()
    {
        $asset = $this->mockVersionedAttribute(['height' => 1]);
        $this->assertEquals(1, $asset->getHeight());
        $this->assertInternalType('int', $asset->getHeight());
    }

    protected function mockVersionedAttribute($attrs)
    {
        $version = new AssetVersion($attrs);

        $asset = $this->getMock(Asset::class, ['getLatestVersion']);
        $asset
            ->expects($this->any())
            ->method('getLatestVersion')
            ->will($this->returnValue($version));

        return $asset;
    }

    protected function getAsset($attrs = [], $methods = null)
    {
        return $this->getMockBuilder(Asset::class)
            ->setConstructorArgs([$attrs])
            ->setMethods($methods)
            ->getMock();
    }
}
