<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site as SiteModel;
use BoomCMS\Repositories\Site as SiteRepository;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Collection;
use Mockery as m;

class SiteTest extends AbstractTestCase
{
    /**
     * @var SiteModel
     */
    protected $model;

    /**
     * @var SiteRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(SiteModel::class);
        $this->repository = new SiteRepository($this->model);
    }

    public function testDelete()
    {
        $model = m::mock(SiteModel::class);
        $model->shouldReceive('delete')->once();

        $this->assertEquals($this->repository, $this->repository->delete($model));
    }

    public function testFind()
    {
        $id = 1;

        $this->model
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturnSelf();

        $this->assertEquals($this->model, $this->repository->find($id));
    }

    public function testFindAll()
    {
        $collection = new Collection();

        $this->model
            ->shouldReceive('all')
            ->once()
            ->andReturn($collection);

        $this->assertEquals($collection, $this->repository->findAll());
    }

    public function testFindByHostname()
    {
        $hostname = 'test.com';
        $site = new SiteModel();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with(SiteModel::ATTR_HOSTNAME, '=', $hostname)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($site);

        $this->assertEquals($site, $this->repository->findByHostname($hostname));
    }

    public function testFindByPerson()
    {
        $sites = [new SiteModel(), new SiteModel()];
        $person = new Person();
        $person->{Person::ATTR_ID} = 1;

        $this->model
            ->shouldReceive('join')
            ->once()
            ->with('person_site', 'person_site.site_id', '=', 'sites.id')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with('person_site.person_id', '=', $person->getId())
            ->andReturnSelf();

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with('name', 'asc')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('all')
            ->once()
            ->andReturn($sites);

        $this->assertEquals($sites, $this->repository->findByPerson($person));
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
