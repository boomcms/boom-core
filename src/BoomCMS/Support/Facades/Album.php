<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Album as AlbumRepository;
use Illuminate\Support\Facades\Facade;

class Album extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AlbumRepository::class;
    }
}
