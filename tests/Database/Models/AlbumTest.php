<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Album;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Traits\SingleSite;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mockery as m;

class AlbumTest extends AbstractModelTestCase
{
    use Traits\HasCreatedByTests;
    use Traits\HasFeatureImageTests;

    /**
     * @var string
     */
    protected $model = Album::class;

    public function testIsSingleSite()
    {
        $this->assertHasTrait($this->model, SingleSite::class);
    }

    public function testSoftDeletes()
    {
        $this->assertHasTrait($this->model, SoftDeletes::class);
    }

    public function testAssetCountIsGuarded()
    {
        $album = new Album();

        $album->fill([
            Album::ATTR_ASSET_COUNT => 1,
        ]);
    }

    public function testAssetCountIsZeroByDefault()
    {
        $album = new Album();

        $this->AssertEquals(0, $album->getAssetCount());
    }

    public function testAssets()
    {
        $album = m::mock(Album::class)->makePartial();

        $album
            ->shouldReceive('belongsToMany')
            ->once()
            ->with(Asset::class);

        $album->assets();
    }

    public function testAddAssets()
    {
        $album = m::mock(Album::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $assetIds = [1, 2, 3];

        $album
            ->shouldReceive('assets')
            ->once()
            ->andReturnSelf();

        $album
            ->shouldReceive('attach')
            ->once()
            ->with($assetIds)
            ->andReturnSelf();

        $this->assertEquals($album, $album->addAssets($assetIds));
    }

    public function testRemoveAsset()
    {
        $album = m::mock(Album::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $assetIds = [1, 2, 3];

        $album
            ->shouldReceive('assets')
            ->once()
            ->andReturnSelf();

        $album
            ->shouldReceive('detach')
            ->once()
            ->with($assetIds)
            ->andReturnSelf();

        $this->assertEquals($album, $album->removeAssets($assetIds));
    }

    public function testUpdateAssetCount()
    {
        $this->markTestIncomplete();
    }
}
