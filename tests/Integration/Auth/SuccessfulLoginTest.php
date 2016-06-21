<?php

namespace BoomCMS\Tests\Integration\Auth;

use BoomCMS\Database\Models\Person;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\AbstractTestCase;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Mockery as m;

class SuccessfulLoginTest extends AbstractTestCase
{
    public function testLastLoginTimeIsNotUpdatedWhenLoginIsNotByCMSUser()
    {
        $person = m::mock(Person::class)->makePartial();
        $person->shouldReceive('setLastLogin')->never();

        PersonFacade::shouldReceive('save')->never();

        Auth::login($person);

        $authenticatable = m::mock(Authenticatable::class)->makePartial();
        $authenticatable->shouldReceive('getAuthIdentifier')->andReturn('boomcms');

        Auth::login($authenticatable);
    }

    public function testLastLoginTimeIsUpdatedWhenValidPersonLogsIn()
    {
        $person = m::mock(Person::class)->makePartial();
        $person->{Person::ATTR_ID} = 1;

        $person
            ->shouldReceive('setLastLogin')
            ->once()
            ->with(m::on(function (Carbon $time) {
                return $time->getTimestamp() === time();
            }));

        PersonFacade::shouldReceive('save')
            ->once()
            ->with($person);

        Auth::login($person);
    }
}
