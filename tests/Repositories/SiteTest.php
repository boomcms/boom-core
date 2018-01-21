<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site as SiteModel;
use BoomCMS\Repositories\Site as SiteRepository;
use Illuminate\Database\Eloquent\Collection;
use Mockery as m;

class SiteTest extends BaseRepositoryTest
{
    protected $modelClass = SiteModel::class;

    public function setUp()
    {
        parent::setUp();

        $this->repository = new SiteRepository($this->model);
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
        $sites = collect([new SiteModel(), new SiteModel()]);
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
}
