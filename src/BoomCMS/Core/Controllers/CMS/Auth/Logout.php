<?php

namespace BoomCMS\Core\Controllers\CMS\Auth;

use BoomCMS\Core\Controllers\Controller;

class Logout extends Controller
{
    public function index()
    {
        // This needs to happen before we log the user out, or we don't be able to log who logged out.
//        $this->_log_logout();

        $this->auth->logout();

        // TODO: make sure response code is 303.
        return redirect()->back();
    }
}
