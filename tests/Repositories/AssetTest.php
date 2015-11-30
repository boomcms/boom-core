<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Repositories\Asset as AssetRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class AssetTest extends AbstractTestCase
{
    public function testFindReturnsAssetById()
    {
        $asset = m::mock(Asset::class);
        $model = m::mock(Asset::class);

        $model->shouldReceive('find')
            ->with(1)
            ->andReturn($asset);

        $repository = new AssetRepository($model);

        $this->assertEquals($asset, $repository->find(1));
    }
}
