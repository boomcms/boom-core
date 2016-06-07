<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\Person as PersonModel;
use BoomCMS\Support\Facades\Person;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login as LoginEvent;

class LogSuccessfulLogin
{
    /**
     * @param LoginEvent $event
     */
    public function handle(LoginEvent $event)
    {
        $person = $event->user;

        if ($person instanceof PersonModel && $person->getId()) {
            $person->setLastLogin(Carbon::now());

            Person::save($person);
        }
    }
}
