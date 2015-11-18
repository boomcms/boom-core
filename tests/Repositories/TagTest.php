<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Tag;
use BoomCMS\Repositories\Tag as TagRepository;
use BoomCMS\Tests\AbstractTestCase;
use \Mockery as m;

class TagTest extends AbstractTestCase
{
    public function create()
    {
        $name = 'test name';
        $group = 'test group';

        $model = m::mock(Tag::class);
        $model->shouldReceive('create')->with($name, $group)->andReturn($model);

        $repository = new TagRepository($model);
        
        $this->assertEquals($model, $repository->create($name, $group));
    }

    public function testFind()
    {
        $model = m::mock(Tag::class);
        $model->shouldReceive('find')->with(1)->andReturnSelf();

        $repository = new TagRepository($model);
        
        $this->assertEquals($model, $repository->find(1));
    }

    public function testFindByName()
    {
        $model = m::mock(Tag::class);
        $model->shouldReceive('where')->with('name', '=', 'test')->andReturnSelf();
        $model->shouldReceive('first')->andReturnSelf();

        $repository = new TagRepository($model);
        
        $this->assertEquals($model, $repository->findByName('test'));
    }

    public function testFindByNameAndGroup()
    {
        $model = m::mock(Tag::class);
        $model->shouldReceive('where')
            ->with(m::any('name', 'group'), '=', m::any('test name', 'test group'))
            ->andReturnSelf();
        $model->shouldReceive('first')->andReturnSelf();

        $repository = new TagRepository($model);
        
        $this->assertEquals($model, $repository->findByNameAndGroup('test name', 'test group'));
    }

    public function testFindOrCreateByNameAndGroupReturnsExisting()
    {
        $name = 'name';
        $group = 'group';

        $model = new Tag();
        $repository = $this->getMock(TagRepository::class, ['findByNameAndGroup'], [$model]);

        $repository
            ->expects($this->once())
            ->method('findByNameAndGroup')
            ->with($this->equalTo($name), $this->equalTo($group))
            ->will($this->returnValue($model));

        $this->assertEquals($model, $repository->findOrCreateByNameAndGroup($name, $group));
    }

    public function testFindOrCreateByNameAndGroupReturnsNew()
    {
        $name = 'name';
        $group = 'group';

        $model = new Tag();
        $repository = $this
            ->getMock(TagRepository::class, ['findByNameAndGroup', 'create'], [$model]);

        $repository
            ->expects($this->once())
            ->method('findByNameAndGroup')
            ->with($this->equalTo($name), $this->equalTo($group))
            ->will($this->returnValue(false));

        $repository
            ->expects($this->once())
            ->method('create')
            ->with($this->equalTo($name), $this->equalTo($group))
            ->will($this->returnValue($model));

        $this->assertEquals($model, $repository->findOrCreateByNameAndGroup($name, $group));
    }
}
