<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Helpers;

class Search extends Controller
{
    public function getPages()
    {
        return Helpers::getPages($this->request->input());
    }
}
