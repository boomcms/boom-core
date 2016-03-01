<?php

namespace BoomCMS\Support\Helpers;

use Illuminate\Support\Facades\Config as c;

abstract class Config
{
    /**
     * Recursively merges a file into the boomcms config group.
     *
     * @param string $file
     */
    public static function merge($file)
    {
        if (file_exists($file)) {
            $config = c::get('boomcms', []);
            c::set('boomcms', array_merge_recursive(include $file, $config));
        }
    }
}
