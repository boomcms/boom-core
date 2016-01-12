<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Site as SiteModel;
use BoomCMS\Repositories\Site as SiteRepository;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Collection;
use Mockery as m;

class SiteTest extends AbstractTestCase
{
    public function testDelete()
    {
        $model = m::mock(SiteModel::class);
        $model->shouldReceive('delete')->once();

        $repository = new SiteRepository($model);

        $this->assertEquals($repository, $repository->delete($model));
    }

    public function testFind()
    {
        $id = 1;

        $model = m::mock(SiteModel::class);
        $model
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturnSelf();

        $repository = new SiteRepository($model);

        $this->assertEquals($model, $repository->find($id));
    }

    public function testFindAll()
    {
        $collection = m::mock(Collection::class);

        $model = m::mock(SiteModel::class);
        $model
            ->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $repository = new SiteRepository($model);

        $this->assertEquals($collection, $repository->findAll());
    }

    public function testFindByHostname()
    {
        $hostname = 'test.com';
        $site = new SiteModel();

        $model = m::mock(SiteModel::class);
        $model
            ->shouldReceive('where')
            ->once()
            ->with(SiteModel::ATTR_HOSTNAME, '=', $hostname)
            ->andReturnSelf();

        $model
            ->shouldReceive('first')
            ->once()
            ->andReturn($site);

        $repository = new SiteRepository($model);

        $this->assertEquals($site, $repository->findByHostname($hostname));
    }

    public function testFindByPerson()
    {
        $this->markTestIncomplete();
    }

    public function testFindDefault()
    {
        $site = new SiteModel();

        $model = m::mock(SiteModel::class);
        $model
            ->shouldReceive('where')
            ->once()
            ->with(SiteModel::ATTR_DEFAULT, '=', true)
            ->andReturnSelf();

        $model
            ->shouldReceive('first')
            ->once()
            ->andReturn($site);

        $repository = new SiteRepository($model);

        $this->assertEquals($site, $repository->findDefault());
    }

    public function testMakeDefault()
    {
        $default = m::mock(SiteModel::class)->makePartial();
        $default->{SiteModel::ATTR_ID} = 1;

        $model = m::mock(SiteModel::class);
        $repository = m::mock(SiteRepository::class.'[save]', [$model]);

        $model
            ->shouldReceive('where')
            ->once()
            ->with(SiteModel::ATTR_ID, '!=', $default->getId())
            ->andReturnSelf();

        $model
            ->shouldReceive('update')
            ->once()
            ->with([
                SiteModel::ATTR_DEFAULT => false,
            ]);

        $default
            ->shouldReceive('setDefault')
            ->once()
            ->with(true);

        $repository
            ->shouldReceive('save')
            ->once()
            ->with($default);

        $repository->makeDefault($default);
    }

    public function testMakeDefaultIgnoresIfSiteIsAlreadyDefault()
    {
        $default = m::mock(SiteModel::class)->makePartial();
        $default->{SiteModel::ATTR_ID} = 1;
        $default->{SiteModel::ATTR_DEFAULT} = true;

        $model = m::mock(SiteModel::class);
        $repository = m::mock(SiteRepository::class.'[save]', [$model]);

        $model->shouldReceive('where')->never();
        $model->shouldReceive('update')->never();
        $default->shouldReceive('setDefault')->never();
        $repository->shouldReceive('save')->never()->with($default);

        $repository->makeDefault($default);
    }

    public function testSave()
    {
        $model = m::mock(SiteModel::class);
        $repository = new SiteRepository($model);

        $model->shouldReceive('save')->once();

        $repository->save($model);
    }
}
