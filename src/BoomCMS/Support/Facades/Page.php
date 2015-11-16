<?php

namespace BoomCMS\Support\Facades;

use Illuminate\Support\Facades\Facade;

class Page extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boomcms.repositories.page';
    }
}
