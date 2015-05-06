<?php

class Controller_Cms_Auth_Logout extends Controller_Cms_Auth
{
    public function index()
    {
        // This needs to happen before we log the user out, or we don't be able to log who logged out.
        $this->_log_logout();

        $this->auth->logout(true);

        // TODO: make sure response code is 303.
        redirect()->back();
    }
}
