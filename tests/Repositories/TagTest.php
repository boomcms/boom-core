<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Tag;
use BoomCMS\Repositories\Tag as TagRepository;
use BoomCMS\Tests\AbstractTestCase;
use InvalidArgumentException;
use Mockery as m;

class TagTest extends AbstractTestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateNameCannotBeEmpty()
    {
        $model = m::mock(Tag::class);
        $model->shouldReceive('create')->never();

        $repository = new TagRepository($model, $this->site);

        $this->assertEquals($model, $repository->create('', ''));
    }

    public function testCreate()
    {
        $name = 'test name';
        $group = 'test group';

        $model = m::mock(Tag::class);
        $model
            ->shouldReceive('create')
            ->with([
                Tag::ATTR_NAME  => $name,
                Tag::ATTR_GROUP => $group,
            ])
            ->andReturn($model);

        $repository = new TagRepository($model, $this->site);

        $this->assertEquals($model, $repository->create($name, $group));
    }

    public function testFind()
    {
        $model = m::mock(Tag::class);
        $model->shouldReceive('find')->with(1)->andReturnSelf();

        $repository = new TagRepository($model, $this->site);

        $this->assertEquals($model, $repository->find(1));
    }

    public function testFindByName()
    {
        $model = m::mock(Tag::class);
        $model
            ->shouldReceive('whereSiteIs')
            ->once()
            ->with($this->site)
            ->andReturnSelf();

        $model
            ->shouldReceive('where')
            ->once()
            ->with(Tag::ATTR_NAME, '=', 'test')
            ->andReturnSelf();

        $model->shouldReceive('first')->andReturnSelf();

        $repository = new TagRepository($model, $this->site);

        $this->assertEquals($model, $repository->findByName('test'));
    }

    public function testFindByNameAndGroup()
    {
        $name = 'test name';
        $group = 'test group';

        $model = m::mock(Tag::class);
        $model
            ->shouldReceive('whereSiteIs')
            ->once()
            ->with($this->site)
            ->andReturnSelf();

        $model
            ->shouldReceive('where')
            ->once()
            ->with(Tag::ATTR_NAME, '=', $name)
            ->andReturnSelf();

        $model
            ->shouldReceive('where')
            ->once()
            ->with(Tag::ATTR_GROUP, '=', $group)
            ->andReturnSelf();

        $model->shouldReceive('first')->andReturnSelf();

        $repository = new TagRepository($model, $this->site);

        $this->assertEquals($model, $repository->findByNameAndGroup($name, $group));
    }

    public function testFindBySite()
    {
        $model = m::mock(Tag::class);

        $model
            ->shouldReceive('select')
            ->once()
            ->with('tags.*')
            ->andReturnSelf();

        $model
            ->shouldReceive('where')
            ->once()
            ->with('tags.site_id', $this->site->getId())
            ->andReturnSelf();

        $model
            ->shouldReceive('appliedToALivePage')
            ->once()
            ->andReturnSelf();

        $model->shouldReceive('orderBy')->with('group')->andReturnSelf();
        $model->shouldReceive('orderBy')->with('name')->andReturnSelf();
        $model->shouldReceive('get')->andReturnSelf();

        $repository = new TagRepository($model, $this->site);

        $this->assertEquals($model, $repository->findBySite($this->site));
    }

    public function testFindBySlugAndGroup()
    {
        $slug = 'test-slug';
        $group = 'test group';
        $model = m::mock(Tag::class);

        $model
            ->shouldReceive('whereSiteIs')
            ->once()
            ->with($this->site)
            ->andReturnSelf();

        $model
            ->shouldReceive('where')
            ->once()
            ->with(Tag::ATTR_SLUG, '=', $slug)
            ->andReturnSelf();

        $model
            ->shouldReceive('where')
            ->once()
            ->with(Tag::ATTR_GROUP, '=', $group)
            ->andReturnSelf();

        $model->shouldReceive('first')->andReturnSelf();

        $repository = new TagRepository($model, $this->site);
        $this->assertEquals($model, $repository->findBySlugAndGroup($slug, $group));
    }

    public function testfindOrCreateReturnsExisting()
    {
        $name = 'name';
        $group = 'group';
        $model = new Tag();
        $repository = m::mock(TagRepository::class.'[findByNameAndGroup]', [$model, $this->site]);

        $repository
            ->shouldReceive('findByNameAndGroup')
            ->once()
            ->with($name, $group)
            ->andReturn($model);

        $this->assertEquals($model, $repository->findOrCreate($name, $group));
    }

    public function testfindOrCreateReturnsNew()
    {
        $name = 'name';
        $group = 'group';
        $model = new Tag();
        $repository = m::mock(TagRepository::class.'[findByNameAndGroup,create]', [$model, $this->site]);

        $repository
            ->shouldReceive('findByNameAndGroup')
            ->with($name, $group)
            ->andReturn(null);

        $repository
            ->shouldReceive('create')
            ->with($name, $group)
            ->andReturn($model);

        $this->assertEquals($model, $repository->findOrCreate($name, $group));
    }
}
