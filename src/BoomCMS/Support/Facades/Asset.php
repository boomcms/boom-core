<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Asset as AssetRepository;
use Illuminate\Support\Facades\Facade;

class Asset extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AssetRepository::class;
    }
}
