<?php

namespace BoomCMS\Http\Controllers\Auth;

use BoomCMS\Auth\Hasher;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordReset extends Controller
{
    use ResetsPasswords;

    protected $subject = 'BoomCMS Password Reset';
    protected $linkRequestView = 'boomcms::auth.password';
    protected $resetView = 'boomcms::auth.reset';
    protected $redirectPath = '/';

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string                                      $password
     *
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->password = (new Hasher())->make($password);

        Person::save($user);
        auth()->login($user);
    }
}
