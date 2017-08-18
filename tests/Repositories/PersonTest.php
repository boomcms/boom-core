<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories\Person as PersonRepository;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class PersonTest extends BaseRepositoryTest
{
    protected $modelClass = Person::class;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(Person::class.'[where,join,whereSite,destroy,orderBy,get,with,delete]');
        $this->repository = m::mock(PersonRepository::class, [$this->model, $this->site])->makePartial();
    }

    public function testFindByGroupId()
    {
        $this->model->shouldReceive('join')->with('group_person', 'people.id', '=', 'person_id')->andReturnSelf();
        $this->model->shouldReceive('where')->with('group_id', '=', 1)->andReturnSelf();
        $this->model->shouldReceive('orderBy')->with('name', 'asc')->andReturnSelf();
        $this->model->shouldReceive('get')->andReturn([]);

        $this->assertEquals([], $this->repository->findByGroupId(1));
    }

    public function testFindBySite()
    {
        $site = new Site();
        $site->{Site::ATTR_ID} = 1;
        $people = [new Person(), new Person()];

        $this->model
            ->shouldReceive('with')
            ->once()
            ->with('groups')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('with')
            ->once()
            ->with('sites')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('whereSite')
            ->once()
            ->with($site)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn($people);

        $this->assertEquals($people, $this->repository->findBySite($site));
    }

    public function testGetAssetUploaders()
    {
        $people = collect([]);

        $this->model
            ->shouldReceive('has')
            ->once()
            ->with('assets')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('orderBy')
            ->once()
            ->with(Person::ATTR_NAME, 'asc')
            ->andReturnSelf();

        $this->model
            ->shouldReceive('get')
            ->once()
            ->andReturn($people);

        $this->assertEquals($people, $this->repository->getAssetUploaders());
    }

    public function testRetrieveByCredentials()
    {
        $query = m::mock(Builder::class);
        $person = new Person();
        $credentials = [
            'email' => 'test@test.com',
        ];

        $this->model
            ->shouldReceive('whereSite')
            ->once()
            ->with($this->site)
            ->andReturn($query);

        $query
            ->shouldReceive('where')
            ->once()
            ->with(Person::ATTR_EMAIL, '=', $credentials['email'])
            ->andReturnSelf();

        $query
            ->shouldReceive('first')
            ->once()
            ->andReturn($person);

        $this->assertEquals($person, $this->repository->retrieveByCredentials($credentials));
    }

    public function testRetrieveById()
    {
        $id = 1;
        $person = new Person();

        $this->repository
            ->shouldReceive('find')
            ->once()
            ->with($id)
            ->andReturn($person);

        $this->assertEquals($person, $this->repository->retrieveById($id));
    }

    public function testRetrieveByToken()
    {
        $person = new Person();
        $personId = 1;
        $token = 'test';

        $this->model
            ->shouldReceive('whereSite')
            ->once()
            ->with($this->site)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with($person->getKeyName(), '=', $personId)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('where')
            ->once()
            ->with($person->getRememberTokenName(), '=', $token)
            ->andReturnSelf();

        $this->model
            ->shouldReceive('first')
            ->once()
            ->andReturn($person);

        $this->assertEquals($person, $this->repository->retrieveByToken($personId, $token));
    }

    public function testSave()
    {
        $person = m::mock(Person::class);
        $person->shouldReceive('save');

        $repository = new PersonRepository(new Person(), $this->site);

        $this->assertEquals($person, $repository->save($person));
    }

    public function testValidateCredentialsWithUserWithNoPassword()
    {
        $person = new Person([
            Person::ATTR_EMAIL => 'test@test.com',
        ]);

        $repository = new PersonRepository(new Person(), $this->site);

        $this->assertFalse($repository->validateCredentials($person, [
            'password' => '',
        ]));
    }
}
