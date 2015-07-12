<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Controllers\Controller;
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
        return View::make('boom::pages.index');
    }
}
