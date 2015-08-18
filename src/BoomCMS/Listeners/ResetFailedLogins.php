<?php

namespace BoomCMS\Listeners;

use BoomCMS\Events\SuccessfulLogin;
use BoomCMS\Support\Facades\Person;

class ResetFailedLogins
{
    public function handle(SuccessfulLogin $event)
    {
        $person = $event->getPerson();

        $person->setLastFailedLogin(null);
        $person->setFailedLogins(0);

        Person::save($person);
    }
}