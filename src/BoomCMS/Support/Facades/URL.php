<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\URL as URLRepository;
use Illuminate\Support\Facades\Facade;

class URL extends Facade
{
    protected static function getFacadeAccessor()
    {
        return URLRepository::class;
    }
}
