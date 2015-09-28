<?php

namespace BoomCMS\Listeners;

use BoomCMS\Events\AccountCreated;
use BoomCMS\Foundation\Listeners\EmailNotification;
use BoomCMS\Support\Facades\Settings;

class SendAccountCreatedNotification extends EmailNotification
{
    public function handle(AccountCreated $event)
    {
        $createdBy = $event->getCreatedBy();
        $person = $event->getPerson();

        $this->send($person, 'Welcome to BoomCMS', 'newperson', [
            'person'    => $person,
            'siteName'  => Settings::get('site.name'),
            'password'  => $event->getPassword(),
            'createdBy' => $createdBy->loaded() ? $createdBy->getName() : Settings::get('site.admin.email'),
        ]);
    }
}
