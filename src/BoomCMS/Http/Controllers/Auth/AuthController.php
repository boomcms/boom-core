<?php

namespace BoomCMS\Http\Controllers\Auth;

use Illuminate\Http\Request;
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

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        $redirectTo = session('url.intended') !== '' ? session('url.intended') : $this->redirectPath();

        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($redirectTo);
    }
}
