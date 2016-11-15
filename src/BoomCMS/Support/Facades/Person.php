<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Person as PersonRepository;
use Illuminate\Support\Facades\Facade;

class Person extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PersonRepository::class;
    }
}
