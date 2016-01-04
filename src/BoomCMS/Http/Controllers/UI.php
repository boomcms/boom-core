<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class UI extends Controller
{
    public function getImageEditor()
    {
        return view('boomcms::assets.image_editor');
    }
}
