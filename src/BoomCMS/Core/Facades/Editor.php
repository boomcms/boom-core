<?php

namespace BoomCMS\Core\Facades;

use Illuminate\Support\Facades\Facade;

class Editor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.editor';
    }
}
