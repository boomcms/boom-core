<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.router';
    }
}
