<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Site as SiteRepository;
use Illuminate\Support\Facades\Facade;

class Site extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SiteRepository::class;
    }
}
