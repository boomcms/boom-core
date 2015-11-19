<?php

namespace BoomCMS\Tests\Asset;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Support\Facades\Person;
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
        $asset = $this->getAsset(['width' => 1]);
        $this->assertEquals(1, $asset->getWidth());
        $this->assertInternalType('int', $asset->getWidth());
    }

    public function testGetHeight()
    {
        $asset = $this->getAsset(['height' => 1]);
        $this->assertEquals(1, $asset->getHeight());
        $this->assertInternalType('int', $asset->getHeight());
    }

    public function testGetUploadedBy()
    {
        Person::shouldReceive('find')
            ->with(1)
            ->andReturn('a person');

        $asset = $this->getAsset(['uploaded_by' => 1]);
        $this->assertEquals('a person', $asset->getUploadedBy());
    }

    protected function getAsset($attrs = [], $methods = null)
    {
        return $this->getMockBuilder(Asset::class)
            ->setConstructorArgs([$attrs])
            ->setMethods($methods)
            ->getMock();
    }
}
