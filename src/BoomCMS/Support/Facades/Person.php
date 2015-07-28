<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Person extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.person.provider';
    }
}
