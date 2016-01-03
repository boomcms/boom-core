<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AuthController extends Controller
{
    use AuthenticatesUsers;
    use ThrottlesLogins;
    use ValidatesRequests;

    protected $loginPath = '/cms/login';
}
