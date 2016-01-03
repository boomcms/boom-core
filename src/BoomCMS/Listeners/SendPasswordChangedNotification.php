<?php

namespace BoomCMS\Listeners;

use BoomCMS\Events\Auth\PasswordChanged;
use BoomCMS\Foundation\Listeners\EmailNotification;

class SendPasswordChangedNotification extends EmailNotification
{
    public function handle(PasswordChanged $event)
    {
        $person = $event->getPerson();

        $this->send($person, 'BoomCMS Password Changed', 'password_changed', [
            'person' => $person,
        ]);
    }
}
