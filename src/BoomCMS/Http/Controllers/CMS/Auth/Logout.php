<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Events\Auth\Logout as LogoutEvent;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

class Logout extends Controller
{
    public function index()
    {
        Event::fire(new LogoutEvent($this->person, $this->request));

        $url = Session::get('boomcms.redirect_url');

        $this->auth->logout();

        return $url ? redirect()->to($url) : redirect()->back();
    }
}
