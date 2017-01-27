<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use DateTime;
use Mockery as m;

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
        $asset = m::mock(Asset::class)->makePartial();
        $asset->shouldReceive('getWidth')->andReturn(4);
        $asset->shouldReceive('getHeight')->andReturn(3);

        $this->assertEquals(4 / 3, $asset->getAspectRatio());
    }

    public function testGetAspectRatioReturnsZeroWhenAssetHasNoHeight()
    {
        $asset = m::mock(Asset::class)->makePartial();

        $asset
            ->shouldReceive('getHeight')
            ->once()
            ->andReturn(0);

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
        $asset = m::mock(Asset::class)->makePartial();

        $asset
            ->shouldReceive('getLatestVersionId')
            ->andReturn(1);

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

    public function testSetVersion()
    {
        $asset = new Asset();
        $version = new AssetVersion();

        $asset->setVersion($version);

        $this->assertEquals($version, $asset->getLatestVersion());
    }

    /**
     * When downloading an asset the filename cannot contain a forward slash or double back slash.
     * 
     * These are now removed when the asset is created
     *
     * Also removing when retrieving the filename ensures any pre-existing records are valid.
     */
    public function testGetOriginalFilenameRemovesSlashes()
    {
        $filenames = ["test file\'s filename", 'file / name'];

        foreach ($filenames as $filename) {
            $asset = $this->mockVersionedAttribute([
                AssetVersion::ATTR_FILENAME => $filename,
            ]);

            $this->assertEquals(str_replace(['/', '\\'], '', $filename), $asset->getOriginalFilename());
        }
    }

    protected function mockVersionedAttribute($attrs)
    {
        $version = new AssetVersion($attrs);

        $asset = m::mock(Asset::class)->makePartial();
        $asset->shouldReceive('getLatestVersion')->andReturn($version);

        return $asset;
    }
}
