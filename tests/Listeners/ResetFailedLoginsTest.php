<?php

namespace BoomCMS\Tests\Listeners;

use BoomCMS\Database\Models\Person;
use BoomCMS\Events\Auth\SuccessfulLogin;
use BoomCMS\Listeners\ResetFailedLogins;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery as m;

class ResetFailedLoginsTest extends AbstractTestCase
{
    public function testFailedLoginsAreReset()
    {
        $person = $this->getMock(Person::class, ['setFailedLogins', 'setLastFailedLogin'], [[]]);

        $person
            ->expects($this->once())
            ->method('setFailedLogins')
            ->with($this->equalTo(0));

        $person
            ->expects($this->once())
            ->method('setLastFailedLogin')
            ->with($this->equalTo(null));

        PersonFacade::shouldReceive('save')->with($person);

        $event = new SuccessfulLogin($person, m::mock(Request::class));

        $listener = new ResetFailedLogins();
        $listener->handle($event);
    }
}
