<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Chunk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.chunk';
    }
}
