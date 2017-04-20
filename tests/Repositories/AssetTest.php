<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Repositories\Asset as AssetRepository;
use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery as m;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AssetTest extends AbstractTestCase
{
    /**
     * @var Asset
     */
    protected $asset;

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
        $this->asset = m::mock(Asset::class);
        $this->versionRepository = new AssetVersionRepository($this->version);

        $this->asset
            ->shouldReceive('getLatestVersionId')
            ->andReturn(1);

        $this->repository = new AssetRepository($this->model, $this->versionRepository, $this->filesystem);
    }

    public function testDelete()
    {
        $assetIds = [1, 2, 3];

        $this->model
            ->shouldReceive('destroy')
            ->once()
            ->with($assetIds);

        $this->repository->delete($assetIds);
    }

    public function testExists()
    {
        foreach ([true, false] as $value) {
            $this->filesystem
                ->shouldReceive('exists')
                ->once()
                ->with($this->asset->getLatestVersionId())
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

    public function testFile()
    {
        $file = 'test file contents';

        $this->filesystem
            ->shouldReceive('get')
            ->once()
            ->with($this->asset->getLatestVersionId())
            ->andReturn($file);

        $this->assertEquals($file, $this->repository->file($this->asset));
    }

    public function testFindReturnsAssetById()
    {
        $this->model
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($this->asset);

        $this->assertEquals($this->asset, $this->repository->find(1));
    }

    public function testFindReturnsNull()
    {
        $this->model
            ->shouldReceive('find')
            ->with(1)
            ->andReturn(null);

        $this->assertNull($this->repository->find(1));
    }

    public function testSaveFileUsesVersionIdAsFileName()
    {
        $file = m::mock(UploadedFile::class);

        $this->filesystem
            ->shouldReceive('putFileAs')
            ->once()
            ->with(null, $file, $this->asset->getLatestVersionId());

        $this->repository->saveFile($this->asset, $file);
    }

    public function testStream()
    {
        $this->filesystem
            ->shouldReceive('readStream')
            ->once()
            ->with($this->asset->getLatestVersionId());

        $this->repository->stream($this->asset);
    }
}
