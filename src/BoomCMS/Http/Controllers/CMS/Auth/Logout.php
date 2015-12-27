<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class Logout extends Controller
{
    public function index()
    {
        $url = Session::get('boomcms.redirect_url');

        $this->auth->logout();

        return $url ? redirect()->to($url) : redirect()->back();
    }
}
