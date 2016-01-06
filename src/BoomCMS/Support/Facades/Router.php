<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Routing\Router as R;
use Illuminate\Support\Facades\Facade;

class Router extends Facade
{
    protected static function getFacadeAccessor()
    {
        return R::class;
    }
}
