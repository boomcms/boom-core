<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.repositories.asset';
    }
}
