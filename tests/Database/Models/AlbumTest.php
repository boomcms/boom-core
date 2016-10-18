<?php

namespace BoomCMS\Tests\Database\Models;

use BoomCMS\Database\Models\Album;
use BoomCMS\Database\Models\Asset;
use Mockery as m;

class AlbumTest extends AbstractModelTestCase
{
    /**
     * @var string
     */
    protected $model = Album::class;

    public function testAssets()
    {
        $album = m::mock(Album::class)->makePartial();

        $album
            ->shouldReceive('belongsToMany')
            ->once()
            ->with(Asset::class);

        $album->assets();
    }

    public function testAddAsset()
    {
        $album = m::mock(Album::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $asset = new Asset();

        $album
            ->shouldReceive('assets')
            ->once()
            ->andReturnSelf();

        $album
            ->shouldReceive('attach')
            ->once()
            ->with($asset)
            ->andReturnSelf();

        $album
            ->shouldReceive('increment')
            ->once()
            ->with(Album::ATTR_ASSET_COUNT);

        $this->assertEquals($album, $album->addAsset($asset));
    }

    public function testRemoveAsset()
    {
        $album = m::mock(Album::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $asset = new Asset();

        $album
            ->shouldReceive('assets')
            ->once()
            ->andReturnSelf();

        $album
            ->shouldReceive('detach')
            ->once()
            ->with($asset)
            ->andReturnSelf();

        $album
            ->shouldReceive('decrement')
            ->once()
            ->with(Album::ATTR_ASSET_COUNT);

        $this->assertEquals($album, $album->removeAsset($asset));
    }
}
