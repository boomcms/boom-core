<?php

namespace BoomCMS\Settings;

use BoomCMS\Support\Facades\Settings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

abstract class Manager
{
    public static function options()
    {
        $options = [];

        foreach (Config::get('boomcms.settingsManagerOptions') as $name => $type) {
            $langPrefix = "boomcms::settings-manager.$name.";

            $options[] = [
                'name'  => $name,
                'label' => Lang::get("{$langPrefix}_label"),
                'type'  => $type,
                'value' => Settings::get($name),
                'info'  => Lang::has("{$langPrefix}_info") ?
                    Lang::get("{$langPrefix}_info") :
                    '',
            ];
        }

        usort($options, function ($a, $b) {
            return ($a['label'] < $b['label']) ? -1 : 1;
        });

        return $options;
    }

    public static function setLanguages()
    {
        $set_languages = (array)Settings::get('site.languages');

        $languages = array();
        if(count($set_languages)) {
            foreach($set_languages as $language => $value) {
                $languages[] = $language;
            }
        }
        
        return $languages;
    }
}
