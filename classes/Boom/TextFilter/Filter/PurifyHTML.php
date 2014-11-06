<?php

namespace Boom\TextFilter\Filter;

use HTMLPurifier;
use Kohana;

class PurifyHTML implements \Boom\TextFilter\Filter
{
    public function filterText($text)
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->loadArray(Kohana::$config->load('htmlpurifier'));

        $purifier = new HTMLPurifier($config);

        return $purifier->purify($text);
    }
}
