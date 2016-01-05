<?php

namespace BoomCMS\Http\Controllers;

use Illuminate\Support\Facades\View;

class UI extends Controller
{
    public function getImageEditor()
    {
        return view('boomcms::assets.image_editor');
    }
}
