<?php

namespace Boom\TextFilter\Filter;

use HTMLPurifier;
use HTMLPurifier_Config;
use Boom\Config;

class PurifyHTML implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->loadArray(Config::get('htmlpurifier'));

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($text);
    }
}
