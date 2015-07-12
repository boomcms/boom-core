<?php

namespace BoomCMS\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.settings';
    }
}
