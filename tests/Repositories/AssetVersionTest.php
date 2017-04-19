<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\AssetVersion;
use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class AssetVersionTest extends AbstractTestCase
{
    /**
     * @var AssetVersion
     */
    protected $model;

    /**
     * @var AssetVersionRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(AssetVersion::class);
        $this->repository = new AssetVersionRepository($this->model);
    }

    public function testCreate()
    {
        $attrs = [];
        $version = new AssetVersion();

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with($attrs)
            ->andReturn($version);

        $this->assertEquals($version, $this->repository->create($attrs));
    }

    public function testFind()
    {
        $version = m::mock(AssetVersion::class);

        $this->model->shouldReceive('find')
            ->with(1)
            ->andReturn($version);

        $this->assertEquals($version, $this->repository->find(1));
    }
}
