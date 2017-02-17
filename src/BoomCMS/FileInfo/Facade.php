<?php

namespace BoomCMS\FileInfo;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeAccessor()
    {
        return FileInfo::class;
    }
}
