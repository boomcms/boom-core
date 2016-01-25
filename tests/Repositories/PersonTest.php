<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Person;
use BoomCMS\Database\Models\Site;
use BoomCMS\Repositories\Person as PersonRepository;
use BoomCMS\Support\Facades\Router;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Database\Eloquent\Builder;
use Mockery as m;

class PersonTest extends AbstractTestCase
{
    /**
     * @var Person
     */
    protected $model;

    /**
     * @var PersonRepository
     */
    protected $repository;

    public function setUp()
    {
        parent::setUp();

        $this->model = m::mock(Person::class.'[where,join,whereSite,destroy,orderBy,get]');
        $this->repository = m::mock(PersonRepository::class, [$this->model])->makePartial();
    }

    public function testDeleteByIds()
    {
        $this->model->shouldReceive('destroy')->once()->with([1, 2]);

        $this->assertEquals($this->repository, $this->repository->deleteByIds([1, 2]));
    }

    /**
     * @expectedException BoomCMS\Exceptions\DuplicateEmailException
     */
    public function testCreateThrowsDuplicateEmailException()
    {
        $email = 'test@test.com';
        $person = new Person(['id' => 1, 'email' => $email]);

        $this->repository
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($person);

        $this->repository->create(['name' => 'test', 'email' => $email]);
    }

    public function testFindByGroupId()
    {
        $this->model->shouldReceive('join')->with('group_person', 'people.id', '=', 'person_id')->andReturnSelf();
        $this->model->shouldReceive('where')->with('group_id', '=', 1)->andReturnSelf();
        $this->model->shouldReceive('orderBy')->with('name', 'asc')->andReturnSelf();
        $this->model->shouldReceive('get')->andReturn([]);

        $this->assertEquals([], $this->repository->findByGroupId(1));
    }

    public function testRetrieveByCredentials()
    {
        $query = m::mock(Builder::class);
        $person = new Person();
        $credentials = [
            'email' => 'test@test.com',
        ];
        $site = new Site();
        Router::shouldReceive('getActiveSite')->once()->andReturn($site);

        $this->model
            ->shouldReceive('whereSite')
            ->once()
            ->with($site)
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
        $site = new Site();
        Router::shouldReceive('getActiveSite')->once()->andReturn($site);

        $this->model
            ->shouldReceive('whereSite')
            ->once()
            ->with($site)
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

        $repository = new PersonRepository(new Person());

        $this->assertEquals($person, $repository->save($person));
    }
}
