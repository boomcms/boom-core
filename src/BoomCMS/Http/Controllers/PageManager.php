<?php

namespace BoomCMS\Http\Controllers;

class PageManager extends Controller
{
    protected $role = 'managePages';

    public function index()
    {
        return view('boomcms::pages.index');
    }
}
