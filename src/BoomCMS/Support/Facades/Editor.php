<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Editor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.editor';
    }
}
