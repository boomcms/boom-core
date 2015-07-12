<?php

namespace BoomCMS\Core\Facades;

use Illuminate\Support\Facades\Facade;

class URL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.url.provider';
    }
}
