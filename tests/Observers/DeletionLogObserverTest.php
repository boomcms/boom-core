<?php

namespace BoomCMS\Tests\Observers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Observers\DeletionLogObserver;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Contracts\Auth\Guard;
use Mockery as m;

class DeletionLogObserverTest extends AbstractTestCase
{
    public function testUserIsSet()
    {
        $person = new Person();
        $person->{Person::ATTR_ID} = 1;

        $guard = m::mock(Guard::class);
        $guard->shouldReceive('user')->once()->andReturn($person);
        $guard->shouldReceive('check')->once()->andReturn(true);

        $model = new Page();

        $observer = new DeletionLogObserver($guard);
        $observer->deleting($model);

        $this->assertEquals($person->getId(), $model->deleted_by);
    }

    public function testUserIsNullWhenNotLoggedIn()
    {
        $guard = m::mock(Guard::class);
        $guard->shouldReceive('check')->once()->andReturn(false);

        $model = new Page();

        $observer = new DeletionLogObserver($guard);
        $observer->deleting($model);

        $this->assertNull($model->deleted_by);
    }
}
