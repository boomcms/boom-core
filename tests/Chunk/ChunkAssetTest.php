<?php

namespace BoomCMS\Tests\Chunk;

use BoomCMS\Chunk\Asset as Chunk;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\Page;
use BoomCMS\Support\Facades\Asset as AssetFacade;
use BoomCMS\Tests\AbstractTestCase;

class ChunkAssetTest extends AbstractTestCase
{
    public function testGetAssetId()
    {
        $assetId = 1;
        $chunk = new Chunk(new Page(), ['asset_id' => $assetId], 'test');

        $this->assertEquals($assetId, $chunk->getAssetId());
    }

    public function testGetAssetIdReturnsInt()
    {
        $chunk = new Chunk(new Page(), [], 'test');

        $this->assertEquals(0, $chunk->getAssetId());
    }

    public function testGetAsset()
    {
        $assetId = 1;
        $asset = new Asset();
        $chunk = new Chunk(new Page(), ['asset_id' => $assetId], 'test');

        AssetFacade::shouldReceive('find')
            ->once()
            ->with($assetId)
            ->andReturn($asset);

        $this->assertEquals($asset, $chunk->getAsset());
        $this->assertEquals($asset, $chunk->getAsset());
    }

    public function testHasContent()
    {
        $values = [
            0    => false,
            null => false,
            1    => true,
        ];

        foreach ($values as $assetId => $hasContent) {
            $chunk = new Chunk(new Page(), ['asset_id' => $assetId], 'test');

            $this->assertEquals($hasContent, $chunk->hasContent());
        }
    }
}
