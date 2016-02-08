<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\PageVersion as PageVersionRepository;
use Illuminate\Support\Facades\Facade;

class PageVersion extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PageVersionRepository::class;
    }
}
