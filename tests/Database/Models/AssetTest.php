<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Album;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class AssetTest extends AbstractModelTestCase
{
    protected $model = Asset::class;

    public function testGetAspectRatio()
    {
        $asset = $this->mockVersionedAttribute(['aspect_ratio' => 2]);
        $this->assertEquals(2, $asset->getAspectRatio());
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

    public function testIsPublic()
    {
        $values = [
            null,
            0,
            'anything else',
            1,
            true,
            '1',
        ];

        foreach ($values as $value) {
            $asset = new Asset([Asset::ATTR_PUBLIC => $value]);

            $this->assertEquals((bool) $value, $asset->isPublic());
        }
    }

    public function testIsPublicByDefault()
    {
        $asset = new Asset();

        $this->assertTrue($asset->isPublic());
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

    public function testGetPublishedAt()
    {
        $now = new DateTime('now');

        $asset = new Asset([Asset::ATTR_PUBLISHED_AT => $now]);

        $this->assertInstanceOf(Carbon::class, $asset->getPublishedAt());
        $this->assertEquals($now->getTimestamp(), $asset->getPublishedAt()->getTimestamp());
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

    public function testWhereAlbumScope()
    {
        $asset = new Asset();
        $query = m::mock(Builder::class);

        $album = new Album();
        $album->{Album::ATTR_ID} = 1;

        $query
            ->shouldReceive('whereHas')
            ->once()
            ->with('albums', m::on(function ($closure) use ($query) {
                $closure($query);

                return true;
            }))
            ->andReturnSelf();

        $query
            ->shouldReceive('where')
            ->once()
            ->with('albums.id', $album->getId());

        $this->assertEquals($query, $asset->scopeWhereAlbum($query, $album));
    }

    protected function mockVersionedAttribute($attrs)
    {
        $version = new AssetVersion($attrs);

        $asset = m::mock(Asset::class)->makePartial();
        $asset->shouldReceive('getLatestVersion')->andReturn($version);

        return $asset;
    }
}
