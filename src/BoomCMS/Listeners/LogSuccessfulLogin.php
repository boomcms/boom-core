<?php

namespace BoomCMS\Listeners;

use BoomCMS\Support\Facades\Person;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login as LoginEvent;

class LogSuccessfulLogin
{
    public function handle(LoginEvent $event)
    {
        $person = $event->user;
        $person->setLastLogin(Carbon::now());

        Person::save($person);
    }
}
