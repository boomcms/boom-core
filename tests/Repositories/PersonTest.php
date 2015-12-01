<?php

namespace BoomCMS\Tests\Repositories;

use BoomCMS\Database\Models\Person;
use BoomCMS\Repositories\Person as PersonRepository;
use BoomCMS\Tests\AbstractTestCase;
use Mockery as m;

class PersonTest extends AbstractTestCase
{
    public function testDeleteByIds()
    {
        $model = m::mock(Person::class);
        $model->shouldReceive('destroy')->with([1,2]);

        $repository = new PersonRepository($model);

        $this->assertEquals($repository, $repository->deleteByIds([1,2]));
    }

    /**
     * @expectedException BoomCMS\Exceptions\DuplicateEmailException
     */
    public function testCreateThrowsDuplicateEmailException()
    {
        $email = 'test@test.com';
        $person = new Person(['id' => 1, 'email' => $email]);

        $provider = $this->getMockBuilder(PersonRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findByEmail'])
            ->getMock();

        $provider
            ->expects($this->once())
            ->method('findByEmail')
            ->with($this->equalTo($email))
            ->will($this->returnValue($person));

        $provider->create(['name' => 'test', 'email' => $email]);
    }

    public function testFindByGroupId()
    {
        $model = m::mock(Person::class);

        $model->shouldReceive('join')->with('group_person', 'people.id', '=', 'person_id')->andReturnSelf();
        $model->shouldReceive('where')->with('group_id', '=', 1)->andReturnSelf();
        $model->shouldReceive('orderBy')->with('name', 'asc')->andReturnSelf();
        $model->shouldReceive('get')->andReturn([]);

        $repository = new PersonRepository($model);

        $this->assertEquals([], $repository->findByGroupId(1));
    }

    public function testSave()
    {
        $person = m::mock(Person::class);
        $person->shouldReceive('save');

        $repository = new PersonRepository(new Person());

        $this->assertEquals($person, $repository->save($person));
    }
}
