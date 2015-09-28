<?php

namespace BoomCMS\Foundation\Listeners;

use BoomCMS\Core\Person\Person;
use BoomCMS\Support\Facades\Settings;
use Illuminate\Support\Facades\Mail;

abstract class EmailNotification
{
    public function send(Person $to, $subject, $viewName, $viewParams)
    {
        Mail::send("boom::email.$viewName", $viewParams, function ($message) use ($to, $subject) {
            $message
                ->to($to->getEmail(), $to->getName())
                ->from(Settings::get('site.admin.email'), Settings::get('site.name'))
                ->subject($subject);
        });
    }
}