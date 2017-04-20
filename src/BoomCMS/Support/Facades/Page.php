<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Page as PageRepository;
use Illuminate\Support\Facades\Facade;

class Page extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PageRepository::class;
    }
}
