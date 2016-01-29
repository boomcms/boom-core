<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\BoomCMS as B;
use Illuminate\Support\Facades\Facade;

class BoomCMS extends Facade
{
    protected static function getFacadeAccessor()
    {
        return B::class;
    }
}
