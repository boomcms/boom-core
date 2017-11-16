<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Album;
use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Repositories\Asset as AssetRepository;
use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use BoomCMS\Support\Facades\Album as AlbumFacade;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery as m;

class AssetTest extends BaseRepositoryTest
{
    /**
     * @var Asset
     */
    protected $asset;

    /**
     * @var Filesystem
     */
    protected $disk;

    /**
     * @var FilesystemFactory
     */
    protected $fileystems;

    /**
     * @var string
     */
    protected $modelClass = Asset::class;

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

        $this->filesystems = m::mock(FilesystemFactory::class);
        $this->version = new AssetVersion();
        $this->asset = m::mock(Asset::class);
        $this->versionRepository = new AssetVersionRepository($this->version);
        $this->disk = m::mock(Filesysytem::class);

        $this->filesystems
            ->shouldReceive('disk')
            ->andReturn($this->disk);

        $this->asset
            ->shouldReceive('getLatestVersionId')
            ->andReturn(1);

        $this->asset
            ->shouldReceive('getPath')
            ->andReturn('path/to/asset');

        $this->repository = new AssetRepository($this->model, $this->versionRepository, $this->filesystems);
    }

    public function testExists()
    {
        foreach ([true, false] as $value) {
            $this->disk
                ->shouldReceive('exists')
                ->once()
                ->with($this->asset->getPath())
                ->andReturn($value);

            $this->repository->exists($this->asset);
        }
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

    public function testDeleteWithArrayOfIds()
    {
        $album = m::mock(Album::class);

        AlbumFacade::shouldReceive('findByAssetIds')
            ->once()
            ->with([1, 2, 3])
            ->andReturn(collect([$album]));

        $album->shouldReceive('assetsUpdated')->once();

        parent::testDeleteWithArrayOfIds();
    }

    public function testDeleteWithModel()
    {
        $album = m::mock(Album::class);

        $this->model
            ->shouldReceive('getId')
            ->andReturn(1);

        AlbumFacade::shouldReceive('findByAssetIds')
            ->once()
            ->with([$this->model->getId()])
            ->andReturn(collect([$album]));

        $album->shouldReceive('assetsUpdated')->once();

        parent::testDeleteWithModel();
    }

    public function testPath()
    {
        $this->disk
            ->shouldReceive('path')
            ->once()
            ->with($this->asset->getPath());

        $this->assertEquals($this->asset->getPath(), $this->repository->path($this->asset));
    }

    public function testStream()
    {
        $this->disk
            ->shouldReceive('read')
            ->once()
            ->with($this->asset->getPath());

        $this->repository->stream($this->asset);
    }
}
