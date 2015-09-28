<?php

namespace BoomCMS\ServiceProviders;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'BoomCMS\Events\AccountCreated' => [
            'BoomCMS\Listeners\SendAccountCreatedNotification',
        ],
        'BoomCMS\Events\Auth\PasswordChanged' => [
            'BoomCMS\Listeners\SendPasswordChangedNotification',
        ],
        'BoomCMS\Events\Auth\SuccessfulLogin' => [
            'BoomCMS\Listeners\ResetFailedLogins',
        ],
        'BoomCMS\Events\PageWasDeleted' => [
            'BoomCMS\Listeners\RemovePageFromSearch',
        ],
        'BoomCMS\Events\PageWasPublished' => [
            'BoomCMS\Listeners\SaveSearchText',
            'BoomCMS\Listeners\RemoveExpiredSearchTexts',
        ],
        'BoomCMS\Events\PageWasEmbargoed' => [
            'BoomCMS\Listeners\SaveSearchText',
        ],
    ];
}
