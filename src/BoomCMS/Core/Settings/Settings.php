<?php

namespace BoomCMS\Core\Settings;

use Illuminate\Support\Facades\Facade;

class Settings extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'boomcms.settings';
    }
}