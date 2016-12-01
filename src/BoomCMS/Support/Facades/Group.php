<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Group as GroupRepository;
use Illuminate\Support\Facades\Facade;

class Group extends Facade
{
    protected static function getFacadeAccessor()
    {
        return GroupRepository::class;
    }
}
