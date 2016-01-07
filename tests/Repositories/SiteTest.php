<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Site as SiteModel;
use BoomCMS\Repositories\Site as SiteRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class SiteTest extends AbstractTestCase
{
    public function testDelete()
    {
        $model = m::mock(SiteModel::class);
        $model->shouldReceive('delete');

        $repository = m::mock(SiteRepository::class);

        $this->assertEquals($repository, $repository->delete($model));
    }

    public function testFind()
    {
        $id = 1;

        $model = m::mock(SiteModel::class);
        $model
            ->shouldReceive('find')
            ->with($id)
            ->andReturnSelf;

        $repository = new SiteRepository($model);

        $this->assertEquals($model, $repository->find($id));
    }

    public function testFindAll()
    {
        $this->markTestIncomplete();
    }

    public function testFindByHostname()
    {
        $this->markTestIncomplete();
    }

    public function testSave()
    {
        $repository = new GroupRepository();

        $model = m::mock(GroupModel::class);
        $model->shouldReceive('save');

        $repository->save($model);
    }
}
