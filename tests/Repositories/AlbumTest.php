<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Album;
use BoomCMS\Repositories\Album as AlbumRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class AlbumTest extends AbstractTestCase
{
    /**
     * @var Album
     */
    protected $model;

    /**
     * @var AlbumRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(Album::class);

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

    public function testDelete()
    {
        $albumIds = [1, 2, 3];

        $this->model
            ->shouldReceive('destroy')
            ->once()
            ->with($albumIds);

        $this->repository->delete($albumIds);
    }

    public function testFind()
    {
        $albums = [
            1 => new Album(),
            2 => null,
        ];

        foreach ($albums as $albumId => $album) {
            $this->model
                ->shouldReceive('find')
                ->once()
                ->with($albumId)
                ->andReturn($album);

            $this->assertEquals($album, $this->repository->find($albumId));
        }
    }
}
