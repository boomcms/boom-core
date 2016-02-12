<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Repositories\Tag as TagRepository;
use BoomCMS\Tests\AbstractTestCase;
use InvalidArgumentException;
use Mockery as m;

class TagTest extends AbstractTestCase
{
    /**
     * @var Site
     */
    protected $site;

    public function setUp()
    {
        parent::setUp();

        $this->site = new Site();
        $this->site->{Site::ATTR_ID} = 1;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCreateNameCannotBeEmpty()
    {
        $model = m::mock(Tag::class);
        $model->shouldReceive('create')->never();

        $repository = new TagRepository($model);

        $this->assertEquals($model, $repository->create($this->site, '', ''));
    }

    public function testCreate()
    {
        $name = 'test name';
        $group = 'test group';

        $model = m::mock(Tag::class);
        $model
            ->shouldReceive('create')
            ->with([
                Tag::ATTR_SITE  => $this->site->getId(),
                Tag::ATTR_NAME  => $name,
                Tag::ATTR_GROUP => $group,
            ])
            ->andReturn($model);

        $repository = new TagRepository($model);

        $this->assertEquals($model, $repository->create($this->site, $name, $group));
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

        $repository = new TagRepository($model);

        $this->assertEquals($model, $repository->findByName($this->site, 'test'));
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

        $repository = new TagRepository($model);

        $this->assertEquals($model, $repository->findByNameAndGroup($this->site, $name, $group));
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

        $repository = new TagRepository($model);
        $this->assertEquals($model, $repository->findBySlugAndGroup($this->site, $slug, $group));
    }

    public function testfindOrCreateReturnsExisting()
    {
        $name = 'name';
        $group = 'group';
        $model = new Tag();
        $repository = m::mock(TagRepository::class.'[findByNameAndGroup]', [$model]);

        $repository
            ->shouldReceive('findByNameAndGroup')
            ->once()
            ->with($this->site, $name, $group)
            ->andReturn($model);

        $this->assertEquals($model, $repository->findOrCreate($this->site, $name, $group));
    }

    public function testfindOrCreateReturnsNew()
    {
        $name = 'name';
        $group = 'group';
        $model = new Tag();
        $repository = m::mock(TagRepository::class.'[findByNameAndGroup,create]', [$model]);

        $repository
            ->shouldReceive('findByNameAndGroup')
            ->with($this->site, $name, $group)
            ->andReturn(null);

        $repository
            ->shouldReceive('create')
            ->with($this->site, $name, $group)
            ->andReturn($model);

        $this->assertEquals($model, $repository->findOrCreate($this->site, $name, $group));
    }
}
