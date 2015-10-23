<?php

namespace BoomCMS\Tests\Listeners;

use BoomCMS\Core\Person\Person;
use BoomCMS\Events\Auth\SuccessfulLogin;
use BoomCMS\Listeners\ResetFailedLogins;
use BoomCMS\Support\Facades\Person as PersonFacade;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Http\Request;
use Mockery as m;

class ResetFailedLoginsTest extends AbstractTestCase
{
    public function testFailedLoginsIsSetToZero()
    {
        $person = $this->getMock(Person::class);

        $person
            ->expects($this->once())
            ->method('setFailedLogins')
            ->with($this->equalTo(0));

        $this->doHandle($person);
    }

    public function testLastFailedLoginIsNulled()
    {
        $person = $this->getMock(Person::class);

        $person
            ->expects($this->once())
            ->method('setLastFailedLogin')
            ->with($this->equalTo(null));

        $this->doHandle($person);
    }

    public function testPersonIsSaved()
    {
        $person = $this->getMock(Person::class);

        PersonFacade::shouldReceive('save')->with($person);

        $this->doHandle($person);
    }

    protected function doHandle($person)
    {
        $event = new SuccessfulLogin($person, m::mock(Request::class));
        $listener = new ResetFailedLogins();

        $listener->handle($event);
    }
}
