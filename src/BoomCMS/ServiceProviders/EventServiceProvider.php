<?php

namespace BoomCMS\ServiceProviders;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'BoomCMS\Events\SuccessfulLogin' => [
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
