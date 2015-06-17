<?php

namespace BoomCMS\Core\TextFilter;

use \URL as URL;
use \Request as Request;

class MakeInternalLinksRelative implements Filter
{
    public function filterText($text)
    {
        if ($base = URL::base(Request::current())) {
            $text = preg_replace("|<(.*?)href=(['\"])".$base."(.*?)(['\"])(.*?)>|", '<$1href=$2/$3$4$5>', $text);
        }

        return $text;
    }
}
