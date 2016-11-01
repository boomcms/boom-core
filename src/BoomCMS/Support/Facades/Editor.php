<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Editor\Editor as EditorClass;
use Illuminate\Support\Facades\Facade;

class Editor extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EditorClass::class;
    }
}
