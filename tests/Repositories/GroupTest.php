<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories\Group as GroupRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class GroupTest extends AbstractTestCase
{
    /**
     * @var GroupModel
     */
    protected $model;

    /**
     * @var GroupRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(GroupModel::class);
        $this->repository = new GroupRepository($this->model);
    }

    public function testCreate()
    {
        $newGroup = new GroupModel();
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $name = 'test';

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with([
                GroupModel::ATTR_SITE => $site->getId(),
                GroupModel::ATTR_NAME => $name,
            ])
            ->andReturn($newGroup);

        $this->assertEquals($newGroup, $this->repository->create($site, $name));
    }

    public function testDelete()
    {
        $model = m::mock(GroupModel::class);
        $model->shouldReceive('delete')->once();

        $repository = new GroupRepository($model);

        $this->assertEquals($repository, $repository->delete($model));
    }

    public function testFind()
    {
        $group = new GroupModel();
        $id = 1;

        $this->model
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($group);

        $this->assertEquals($group, $this->repository->find($id));
    }

    public function testFindBySite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $groups = [new GroupModel(), new GroupModel()];

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(GroupModel::ATTR_SITE, '=', $site->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with(GroupModel::ATTR_NAME, 'asc')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn($groups);

        $this->assertEquals($groups, $this->repository->findBySite($site));
    }

    public function testSave()
    {
        $group = m::mock(GroupModel::class);
        $group->shouldReceive('save')->once();

        $this->repository->save($group);
    }
}
