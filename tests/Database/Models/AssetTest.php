<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Database\Models\Site;
use DateTime;

class AssetTest extends AbstractModelTestCase
{
    protected $model = Asset::class;

    public function testDirectory()
    {
        $model = new Asset();

        $this->assertEquals(storage_path().'/boomcms/assets', $model->directory());
    }

    public function testGetAspectRatio()
    {
        $asset = $this->getMock(Asset::class, ['getWidth', 'getHeight']);
        $asset->expects($this->any())
            ->method('getWidth')
            ->will($this->returnValue(4));

        $asset->expects($this->any())
            ->method('getHeight')
            ->will($this->returnValue(3));

        $this->assertEquals(4 / 3, $asset->getAspectRatio());
    }

    public function testGetAspectRatioReturnsZeroWhenAssetHasNoHeight()
    {
        $asset = $this->getMock(Asset::class, ['getHeight']);

        $asset->expects($this->once())
            ->method('getHeight')
            ->will($this->returnValue(0));

        $this->assertEquals(1, $asset->getAspectRatio());
    }

    public function testGetCreditsReturnsCreditsAttribute()
    {
        $asset = new Asset([Asset::ATTR_CREDITS => 'test']);
        $this->assertEquals('test', $asset->getCredits());
    }

    public function testGetDescriptionReturnsDescriptionAttribute()
    {
        $asset = new Asset([Asset::ATTR_DESCRIPTION => 'test']);
        $this->assertEquals('test', $asset->getDescription());
    }

    public function testGetDownloadsReturnsDownloadsAttribute()
    {
        $asset = new Asset([Asset::ATTR_DOWNLOADS => 1]);
        $this->assertEquals(1, $asset->getDownloads());
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

    public function testGetFilesize()
    {
        $asset = $this->mockVersionedAttribute(['filesize' => 1000]);
        $this->assertEquals(1000, $asset->getFilesize());
    }

    public function testGetType()
    {
        $asset = new Asset([Asset::ATTR_TYPE => 'image']);
        $this->assertEquals('image', $asset->getType());

        $asset = new Asset([Asset::ATTR_TYPE => 'video']);
        $this->assertEquals('video', $asset->getType());

        $asset = new Asset();
        $this->assertEquals('', $asset->getType());
    }

    public function testIsImage()
    {
        $image = new Asset([Asset::ATTR_TYPE => 'image']);
        $this->assertTrue($image->isImage());

        $notAnImage = new Asset([Asset::ATTR_TYPE => 'video']);
        $this->assertFalse($notAnImage->isImage());

        $empty = new Asset();
        $this->assertFalse($empty->isImage());
    }

    public function testIsVideo()
    {
        $video = new Asset([Asset::ATTR_TYPE => 'video']);
        $this->assertTrue($video->isVideo());

        $image = new Asset([Asset::ATTR_TYPE => 'image']);
        $this->assertFalse($image->isVideo());

        $empty = new Asset();
        $this->assertFalse($empty->isVideo());
    }

    public function testGetMetadata()
    {
        $metadata = ['key1' => 'value1'];
        $asset = $this->mockVersionedAttribute(['metadata' => $metadata]);
        $this->assertEquals($metadata, $asset->getMetadata());

        $asset = $this->mockVersionedAttribute([]);
        $this->assertEquals([], $asset->getMetadata());
    }

    public function testGetMimetype()
    {
        $asset = $this->mockVersionedAttribute(['mimetype' => 'image/png']);
        $this->assertEquals('image/png', $asset->getMimetype());
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

    public function testGetTitleReturnsTitleAttribute()
    {
        $asset = new Asset([Asset::ATTR_TITLE => 'test']);
        $this->assertEquals('test', $asset->getTitle());
    }

    public function testGetThumbnailAssetIdReturnsThumbnailAttribute()
    {
        $asset = new Asset([Asset::ATTR_THUMBNAIL_ID => 1]);
        $this->assertEquals(1, $asset->getThumbnailAssetId());
    }

    public function testGetUploadedTime()
    {
        $now = new DateTime('now');

        $asset = new Asset([Asset::ATTR_UPLOADED_AT => $now->getTimestamp()]);
        $this->assertInstanceOf(DateTime::class, $asset->getUploadedTime());
        $this->assertEquals($now->getTimestamp(), $asset->getUploadedTime()->getTimestamp());
    }

    public function testHasMetadata()
    {
        $metadata = ['key1' => 'value1'];
        $asset = $this->mockVersionedAttribute(['metadata' => $metadata]);
        $this->assertTrue($asset->hasMetadata());

        $asset = $this->mockVersionedAttribute([]);
        $this->assertFalse($asset->hasMetadata());
    }

    public function testSetSite()
    {
        $asset = new Asset();

        $asset->setSite($this->site);

        $this->assertEquals($this->site->getId(), $asset->{Asset::ATTR_SITE});
    }

    public function testSetVersion()
    {
        $asset = new Asset();
        $version = new AssetVersion();

        $asset->setVersion($version);

        $this->assertEquals($version, $asset->getLatestVersion());
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
}
