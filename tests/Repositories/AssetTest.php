<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Repositories\Asset as AssetRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class AssetTest extends AbstractTestCase
{
    public function testExtensions()
    {
        $extensions = ['gif', 'jpeg'];
        $version = m::mock(AssetVersion::class);

        $version
            ->shouldReceive('select')
            ->once()
            ->with(AssetVersion::ATTR_EXTENSION)
            ->andReturnSelf();

        $version
            ->shouldReceive('where')
            ->once()
            ->with(AssetVersion::ATTR_EXTENSION, '!=', '')
            ->andReturnSelf();

        $version
            ->shouldReceive('orderBy')
            ->once()
            ->with(AssetVersion::ATTR_EXTENSION)
            ->andReturnSelf();

        $version
            ->shouldReceive('distinct')
            ->once()
            ->andReturnSelf();

        $version
            ->shouldReceive('pluck')
            ->once()
            ->with(AssetVersion::ATTR_EXTENSION)
            ->andReturn($extensions);

        $repository = new AssetRepository(new Asset(), $version);

        $this->assertEquals($extensions, $repository->extensions());
    }

    public function testFindReturnsAssetById()
    {
        $asset = m::mock(Asset::class);
        $model = m::mock(Asset::class);

        $model->shouldReceive('find')
            ->with(1)
            ->andReturn($asset);

        $repository = new AssetRepository($model, new AssetVersion());

        $this->assertEquals($asset, $repository->find(1));
    }

    public function testFindVersion()
    {
        $version = m::mock(AssetVersion::class);
        $model = m::mock(AssetVersion::class);

        $model->shouldReceive('find')
            ->with(1)
            ->andReturn($version);

        $repository = new AssetRepository(new Asset(), $model);

        $this->assertEquals($version, $repository->findVersion(1));
    }
}
