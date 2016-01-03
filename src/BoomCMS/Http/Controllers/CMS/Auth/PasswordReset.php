<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Auth\Hasher;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Validation\ValidatesRequests;

class PasswordReset extends Controller
{
    use ResetsPasswords;
    use ValidatesRequests;

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
        $user->password = (new Hasher())->make($password);

        Person::save($user);
        auth()->login($user);
    }
}
