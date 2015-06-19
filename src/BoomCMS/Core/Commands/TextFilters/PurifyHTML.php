<?php

namespace BoomCMS\Core\Commands\TextFilters;

use HTMLPurifier;
use HTMLPurifier_Config;

use Illuminate\Support\Facades\Config;

class PurifyHTML extends BaseTextFilter
{
    public function handle()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->loadArray(Config::get('boomcms.htmlpurifier'));

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($this->text);
    }
}
