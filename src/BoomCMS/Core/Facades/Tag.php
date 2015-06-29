<?php

namespace BoomCMS\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Tag extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'boomcms.tag.provider';
    }
}