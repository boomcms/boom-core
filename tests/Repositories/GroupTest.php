<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories\Group as GroupRepository;

class GroupTest extends BaseRepositoryTest
{
    protected $modelClass = GroupModel::class;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new GroupRepository($this->model);
    }

    public function testCreate()
    {
        $newGroup = new GroupModel();
        $name = 'test';

        $this->model
            ->shouldReceive('create')
            ->once()
            ->with([
                GroupModel::ATTR_NAME => $name,
            ])
            ->andReturn($newGroup);

        $this->assertEquals($newGroup, $this->repository->create($name));
    }

    public function testFindAll()
    {
        $groups = collect([new GroupModel()]);

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn($groups);

        $this->assertEquals($groups, $this->repository->findAll());
    }

    public function testFindBySite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $groups = collect([new GroupModel(), new GroupModel()]);

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
}
