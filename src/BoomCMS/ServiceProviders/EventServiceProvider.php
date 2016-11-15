<?php

namespace BoomCMS\ServiceProviders;

use BoomCMS\Database\Models;
use BoomCMS\Observers\CreationLogObserver;
use BoomCMS\Events;
use BoomCMS\Listeners;
use Illuminate\Auth\Events\Login as LoginEvent;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Events\AccountCreated::class => [
            Listeners\SendAccountCreatedNotification::class,
        ],
        Events\Auth\PasswordChanged::class => [
            Listeners\SendPasswordChangedNotification::class,
        ],
        Events\PageSearchSettingsWereUpdated::class => [
            Listeners\UpdateSearchText::class,
        ],
        Events\PageTitleWasChanged::class => [
            Listeners\SetPageInternalName::class,
            Listeners\UpdatePagePrimaryURLToTitle::class,
        ],
        Events\PageWasCreated::class => [
            Listeners\CreatePagePrimaryURL::class,
        ],
        Events\PageWasDeleted::class => [
            Listeners\RemovePageFromSearch::class,
        ],
        Events\PageWasPublished::class => [
            Listeners\SaveSearchText::class,
            Listeners\RemoveExpiredSearchTexts::class,
        ],
        Events\PageWasEmbargoed::class => [
            Listeners\SaveSearchText::class,
        ],
        LoginEvent::class => [
            Listeners\LogSuccessfulLogin::class,
        ],
    ];

    public function boot(Dispatcher $events)
    {
        parent::boot($events);

        Models\Asset::observe(CreationLogObserver::class);
        Models\AssetVersion::observe(CreationLogObserver::class);
        Models\Page::observe(CreationLogObserver::class);
        Models\PageVersion::observe(CreationLogObserver::class);
    }
}
