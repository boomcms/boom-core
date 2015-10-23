<?php

namespace BoomCMS\Tests\Stubs;

use BoomCMS\ServiceProviders\AssetServiceProvider;
use BoomCMS\ServiceProviders\CoreServiceProvider as BaseCoreServiceProvider;
use BoomCMS\ServiceProviders\AuthServiceProvider;
use BoomCMS\ServiceProviders\EditorServiceProvider;
use BoomCMS\ServiceProviders\PersonServiceProvider;
use BoomCMS\ServiceProviders\PageServiceProvider;
use BoomCMS\ServiceProviders\ChunkServiceProvider;
use BoomCMS\ServiceProviders\URLServiceProvider;
use BoomCMS\ServiceProviders\TagServiceProvider;
use BoomCMS\ServiceProviders\EventServiceProvider;
use Illuminate\Html\HtmlServiceProvider;

class CoreServiceProvider extends BaseCoreServiceProvider
{
    protected $serviceProviders = [
        AssetServiceProvider::class,
        PersonServiceProvider::class,
        AuthServiceProvider::class,
        EditorServiceProvider::class,
        PageServiceProvider::class,
        ChunkServiceProvider::class,
        URLServiceProvider::class,
        TagServiceProvider::class,
        EventServiceProvider::class,
        HtmlServiceProvider::class,
    ];
}