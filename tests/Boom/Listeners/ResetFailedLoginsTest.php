<?php

use BoomCMS\Core\Person\Person;
use BoomCMS\Events\SuccessfulLogin;
use BoomCMS\Listeners\ResetFailedLogins;
use BoomCMS\Support\Facades\Person as PersonFacade;
use Illuminate\Http\Request;
use Mockery as m;

class Listeners_ResetFailedLoginsTest extends TestCase
{
    public function testFailedLoginsIsSetToZero()
    {
        $person = $this->getMock(Person::class);

        $person
            ->expects($this->once())
            ->method('setFailedLogins')
            ->with($this->equalTo(0));

        $this->handle($person);
    }
    
    public function testLastFailedLoginIsNulled()
    {
        $person = $this->getMock(Person::class);

        $person
            ->expects($this->once())
            ->method('setLastFailedLogin')
            ->with($this->equalTo(null));

        $this->handle($person);
    }

    public function testPersonIsSaved()
    {
        $person = $this->getMock(Person::class);

        PersonFacade::shouldReceive('save')->with($person);

        $this->handle($person);
    }

    protected function handle($person)
    {
        $event = new SuccessfulLogin($person, m::mock(Request::class));
        $listener = new ResetFailedLogins();

        $listener->handle($event);
    }
}