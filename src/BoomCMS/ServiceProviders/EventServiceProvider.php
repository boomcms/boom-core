<?php

namespace BoomCMS\ServiceProviders;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'BoomCMS\Events\SuccessfulLogin' => [
            'BoomCMS\Listeners\ResetFailedLogins',
        ],
    ];
}
