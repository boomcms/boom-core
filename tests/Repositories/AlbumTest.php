<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Album;
use BoomCMS\Repositories\Album as AlbumRepository;

class AlbumTest extends BaseRepositoryTest
{
    /**
     * @var string
     */
    protected $modelClass = Album::class;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new AlbumRepository($this->model, $this->site);
    }

    public function testAll()
    {
        $albums = collect([new Album(), new Album()]);

        $this->model
            ->shouldReceive('whereSite')
            ->once()
            ->with($this->site)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with(Album::ATTR_NAME, 'asc')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn($albums);

        $this->assertEquals($albums, $this->repository->all());
    }

    public function testCreate()
    {
        $name = 'test album name';
        $description = 'test album description';
        $album = new Album();

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with($name, $description)
            ->andReturn($album);

        $this->assertEquals($album, $this->repository->create($name, $description));
    }

    public function testFindByAssetIds()
    {
        $this->markTestIncomplete();
    }
}
