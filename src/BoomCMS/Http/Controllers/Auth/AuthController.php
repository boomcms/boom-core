<?php

namespace BoomCMS\Http\Controllers\Auth;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $loginPath = '/boomcms/login';
    protected $loginView = 'boomcms::auth.login';
    protected $redirectTo = '/boomcms';

    public function showLoginForm()
    {
        return view($this->loginView);
    }
}
