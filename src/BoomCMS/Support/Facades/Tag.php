<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Tag as TagRepository;
use Illuminate\Support\Facades\Facade;

class Tag extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TagRepository::class;
    }
}
