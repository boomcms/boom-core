<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Repositories\Asset as AssetRepository;
use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery as m;

class AssetTest extends AbstractTestCase
{
    /**
     * @var Filesystem
     */
    protected $fileystem;

    /**
     * @var Asset
     */
    protected $model;

    /**
     * @var AssetRepository
     */
    protected $repository;

    /**
     * @var AssetVersionRepository
     */
    protected $version;

    /**
     * @var AssetVersionRepository
     */
    protected $versionRepository;

    public function setUp()
    {
        parent::setUp();

        $this->filesystem = m::mock(Filesystem::class);
        $this->version = new AssetVersion();
        $this->model = m::mock(Asset::class);
        $this->versionRepository = new AssetVersionRepository($this->version);

        $this->repository = new AssetRepository($this->model, $this->versionRepository, $this->filesystem);
    }

    public function testExtensions()
    {
        $extensions = collect(['gif', 'jpeg']);

        $this->model
            ->shouldReceive('withLatestVersion')
            ->once()
            ->andReturnSelf();

        $this->model
            ->shouldReceive('select')
            ->once()
            ->with('version.'.AssetVersion::ATTR_EXTENSION.' as e')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('having')
            ->once()
            ->with('e', '!=', '')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with('e')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('distinct')
            ->once()
            ->andReturnSelf();

        $this->model
            ->shouldReceive('pluck')
            ->once()
            ->with('e')
            ->andReturn($extensions);

        $this->assertEquals($extensions, $this->repository->extensions());
    }

    public function testFindReturnsAssetById()
    {
        $asset = new Asset();

        $this->model->shouldReceive('find')
            ->with(1)
            ->andReturn($asset);

        $this->assertEquals($asset, $this->repository->find(1));
    }
}
