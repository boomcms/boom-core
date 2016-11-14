<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Helpers;

class Dashboard extends Controller
{
    public function index()
    {
        return view('boomcms::dashboard.index', [
            'person' => auth()->user(),
            'pages'  => Helpers::getPages([
                'limit' => 20,
                'order' => 'date desc',
            ]),
        ]);
    }
}
