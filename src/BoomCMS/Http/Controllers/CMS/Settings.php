<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class Settings extends Controller
{
    public function getIndex()
    {
        return View::make('boom::settings.index');
    }
}