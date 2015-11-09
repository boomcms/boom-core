<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class Pages extends Controller
{
    protected $role = 'manage_pages';

    public function index()
    {
        return View::make('boomcms::pages.index');
    }
}
