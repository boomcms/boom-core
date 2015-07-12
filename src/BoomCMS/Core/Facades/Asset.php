<?php

namespace BoomCMS\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.asset.provider';
    }
}
