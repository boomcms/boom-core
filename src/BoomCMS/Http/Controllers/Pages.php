<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Http\Controllers\Controller;

class Pages extends Controller
{
    protected $role = 'managePages';

    public function index()
    {
        return view('boomcms::pages.index');
    }
}
