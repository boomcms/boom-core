<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

abstract class BaseRepositoryTest extends AbstractTestCase
{
    protected $model;
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock($this->modelClass);
    }

    public function testDelete()
    {
        $this->model
            ->shouldReceive('delete')
            ->once();

        $this->repository->delete($this->model);
    }

    public function testFindReturnsModelById()
    {
        $model = new $this->modelClass;

        $this->model
            ->shouldReceive('find')
            ->with(1)
            ->andReturn($model);

        $this->assertEquals($model, $this->repository->find(1));
    }

    public function testFindReturnsNull()
    {
        $this->model
            ->shouldReceive('find')
            ->with(1)
            ->andReturn(null);

        $this->assertNull($this->repository->find(1));
    }

    public function testSave()
    {
        $model = m::mock($this->modelClass);
        $model->shouldReceive('save')->once();
        
        $this->assertEquals($model, $this->repository->save($model));
    }
}
