<?php

namespace BoomCMS\Http\Controllers\Auth;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    use ThrottlesLogins;

    protected $loginPath = '/boomcms/login';
    protected $loginView = 'boomcms::auth.login';
    protected $guard = 'boomcms';
    protected $redirectTo = '/';
}
