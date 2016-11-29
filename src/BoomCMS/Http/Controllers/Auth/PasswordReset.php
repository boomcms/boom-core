<?php

namespace BoomCMS\Http\Controllers\Auth;

use BoomCMS\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class PasswordReset extends Controller
{
    use ResetsPasswords;

    protected $resetView = 'boomcms::auth.reset';

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  Request  $request
     * @param  string|null  $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view($this->resetView)->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function redirectPath()
    {
        return route('dashboard');
    }
}
