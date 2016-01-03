<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordReset extends Controller
{
    use ResetsPasswords;

    protected $subject = 'BoomCMS Password Reset';

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
        $user->password = auth()->hash($password);

        Person::save($user);
        Auth::login($user);
    }
}
