<?php

namespace BoomCMS\Tests\Observers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Person;
use BoomCMS\Observers\CreationLogObserver;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Contracts\Auth\Guard;
use Mockery as m;

class CreationLogObserverTest extends AbstractTestCase
{
    public function testUserAndTimeAreSet()
    {
        $person = new Person();
        $person->{Person::ATTR_ID} = 1;

        $guard = m::mock(Guard::class);
        $guard->shouldReceive('user')->once()->andReturn($person);
        $guard->shouldReceive('check')->once()->andReturn(true);

        $model = new Page();

        $observer = new CreationLogObserver($guard);
        $observer->creating($model);

        $this->assertEquals(time(), $model->created_at);
        $this->assertEquals($person->getId(), $model->created_by);
    }

    public function testSettingUserAndTimeWhenNotLoggedIn()
    {
        $guard = m::mock(Guard::class);
        $guard->shouldReceive('check')->once()->andReturn(false);

        $model = new Page();

        $observer = new CreationLogObserver($guard);
        $observer->creating($model);

        $this->assertEquals(time(), $model->created_at);
        $this->assertNull($model->created_by);
    }
}
