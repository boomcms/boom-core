<?php

namespace BoomCMS\Jobs\TextFilters;

use Illuminate\Support\Facades\Request;

class MakeInternalLinksRelative extends BaseTextFilter
{
    public function handle()
    {
        if ($base = Request::getHttpHost()) {
            return preg_replace("|<(.*?)href=(['\"])".$base."(.*?)(['\"])(.*?)>|", '<$1href=$2/$3$4$5>', $this->text);
        }

        return $this->text;
    }
}
