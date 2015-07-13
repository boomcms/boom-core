<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Auth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.auth';
    }
}
