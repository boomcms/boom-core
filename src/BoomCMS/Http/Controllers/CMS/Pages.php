<?php

namespace BoomCMS\Http\Controllers\CMS;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class Pages extends Controller
{
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;

        $this->authorization('manage_pages');
    }

    public function index()
    {
        return View::make('boomcms::pages.index');
    }
}
