<?php

namespace BoomCMS\Listeners;

use BoomCMS\Events\AccountCreated;
use BoomCMS\Support\Facades\Settings;
use Illuminate\Support\Facades\Mail;

class SendAccountCreatedNotification
{
    public function handle(AccountCreated $event)
    {
        $createdBy = $event->getCreatedBy();
        $person = $event->getPerson();

        Mail::send('boom::email.newperson', [
                'person'    => $person,
                'siteName'  => Settings::get('site.name'),
                'password'  => $event->getPassword(),
                'createdBy' => $createdBy->loaded() ? $createdBy->getName() : Settings::get('site.admin.email'),
            ], function ($message) use ($person) {
            $message
                ->to($person->getEmail(), $person->getName())
                ->from(Settings::get('site.admin.email'), Settings::get('site.name'))
                ->subject('Welcome to BoomCMS');
        });
    }
}
