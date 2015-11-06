<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class UI extends Controller
{
    public function getImageEditor()
    {
        return View::make('boomcms::assets.image_editor');
    }
}
