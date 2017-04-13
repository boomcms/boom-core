<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\AssetVersion as AssetVersionRepository;
use Illuminate\Support\Facades\Facade;

class AssetVersion extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AssetVersionRepository::class;
    }
}
