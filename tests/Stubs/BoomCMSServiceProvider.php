<?php

namespace BoomCMS\Tests\Stubs;

use BoomCMS\ServiceProviders\AuthServiceProvider;
use BoomCMS\ServiceProviders\BoomCMSServiceProvider as BaseServiceProvider;
use BoomCMS\ServiceProviders\ChunkServiceProvider;
use BoomCMS\ServiceProviders\EditorServiceProvider;
use BoomCMS\ServiceProviders\EventServiceProvider;
use BoomCMS\ServiceProviders\RepositoryServiceProvider;
use BoomCMS\ServiceProviders\RouteServiceProvider;
use BoomCMS\ServiceProviders\SettingsServiceProvider;
use Illuminate\Html\HtmlServiceProvider;

class BoomCMSServiceProvider extends BaseServiceProvider
{
    protected $serviceProviders = [
        RepositoryServiceProvider::class,
        AuthServiceProvider::class,
        EditorServiceProvider::class,
        ChunkServiceProvider::class,
        EventServiceProvider::class,
        HtmlServiceProvider::class,
        RouteServiceProvider::class,
        SettingsServiceProvider::class,
    ];
}
