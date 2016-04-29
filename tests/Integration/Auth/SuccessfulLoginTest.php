<?php

namespace BoomCMS\Tests\Integration\Auth;

use BoomCMS\Database\Models\Person;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\AbstractTestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mockery as m;

class SuccessfulLoginTest extends AbstractTestCase
{
    public function testLastLoginTimeIsUpdatedWhenPersonLogsIn()
    {
        $person = m::mock(Person::class)->makePartial();
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
