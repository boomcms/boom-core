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
            ->shouldReceive('whereSiteIs')
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
            ->with([
                Album::ATTR_NAME        => $name,
                Album::ATTR_DESCRIPTION => $description,
            ])
            ->andReturn($album);

        $this->assertEquals($album, $this->repository->create($name, $description));
    }

    public function testFindByName()
    {
        $albums = [
            'test album name'   => new Album(),
            'no matching album' => null,
        ];

        foreach ($albums as $name => $album) {
            $this->model
                ->shouldReceive('where')
                ->once()
                ->with(Album::ATTR_NAME, $name)
                ->andReturnSelf();

            $this->model
                ->shouldReceive('first')
                ->once()
                ->andReturn($album);

            $this->assertEquals($album, $this->repository->findByName($name));
        }
    }

    public function testFindOrCreate()
    {
        $album = new Album();

        $albums = [
            'test album name'   => $album,
            'no matching album' => null,
        ];

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with([
                Album::ATTR_NAME        => 'no matching album',
                Album::ATTR_DESCRIPTION => null,
            ])
            ->andReturn($album);

        foreach ($albums as $name => $returnValue) {
            $this->model
                ->shouldReceive('where')
                ->once()
                ->with(Album::ATTR_NAME, $name)
                ->andReturnSelf();

            $this->model
                ->shouldReceive('first')
                ->once()
                ->andReturn($returnValue);

            $this->assertEquals($album, $this->repository->findOrCreate($name));
        }
    }
}
