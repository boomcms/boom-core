<?php

namespace BoomCMS\Http\Controllers\Auth;

use BoomCMS\Auth\Hasher;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Response;

class SendResetEmail extends Controller
{
    use SendsPasswordResetEmails;

    protected $linkRequestView = 'boomcms::auth.link-request';

    /**
     * Display the form to request a password reset link.
     *
     * @return Response
     */
    public function showLinkRequestForm()
    {
        return view($this->linkRequestView);
    }

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
