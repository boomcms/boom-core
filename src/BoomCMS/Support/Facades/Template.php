<?php

namespace BoomCMS\Support\Facades;

use BoomCMS\Repositories\Template as TemplateRepository;
use Illuminate\Support\Facades\Facade;

class Template extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TemplateRepository::class;
    }
}
