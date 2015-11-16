<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class URL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.repositories.url';
    }
}
