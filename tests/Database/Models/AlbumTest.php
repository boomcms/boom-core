<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Album;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Traits\SingleSite;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    public function testAssetsUpdated()
    {
        $album = m::mock(Album::class);
        $relation = m::mock(BelongsToMany::class);
        $asset = new Asset([Asset::ATTR_ID => 1]);
        $assetCount = 2;

        $album
            ->shouldReceive('assets')
            ->times(2)
            ->andReturn($relation);

        $relation
            ->shouldReceive('count')
            ->once()
            ->andReturn($assetCount);

        $relation
            ->shouldReceive('first')
            ->once()
            ->andReturn($asset);

        $album
            ->shouldReceive('fill')
            ->once()
            ->with([
                Album::ATTR_ASSET_COUNT   => $assetCount,
                Album::ATTR_FEATURE_IMAGE => $asset->getId(),
            ]);
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

        $this->assertEquals(0, $album->{Album::ATTR_ASSET_COUNT});
    }

    public function testAssets()
    {
        $album = m::mock(Album::class);

        $album
            ->shouldReceive('belongsToMany')
            ->once()
            ->with(Asset::class);

        $album->assets();
    }

    public function testAddAssets()
    {
        $album = m::mock(Album::class);
        $relation = m::mock(BelongsToMany::class);
        $assetIds = [1, 2, 3];

        $album
            ->shouldReceive('assets')
            ->once()
            ->andReturn($relation);

        $album
            ->shouldReceive('assetsUpdated')
            ->once()
            ->andReturnSelf();

        $relation
            ->shouldReceive('syncWithoutDetaching')
            ->once()
            ->with($assetIds);

        $this->assertEquals($album, $album->addAssets($assetIds));
    }

    public function testRemoveAssets()
    {
        $album = m::mock(Album::class);
        $relation = m::mock(BelongsToMany::class);
        $assetIds = [1, 2, 3];

        $album
            ->shouldReceive('assets')
            ->once()
            ->andReturn($relation);

        $album
            ->shouldReceive('assetsUpdated')
            ->once()
            ->andReturnSelf();

        $relation
            ->shouldReceive('detach')
            ->once()
            ->with($assetIds);

        $this->assertEquals($album, $album->removeAssets($assetIds));
    }
}
