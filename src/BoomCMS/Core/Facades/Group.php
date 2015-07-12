<?php

namespace BoomCMS\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Group extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.group.provider';
    }
}
