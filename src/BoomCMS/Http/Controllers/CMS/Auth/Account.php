<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Events\Auth\PasswordChanged;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class Account extends Controller
{
    public function __construct(Request $request, Auth $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->person = $this->auth->getPerson();
    }

    public function getIndex()
    {
        return View::make('boomcms::account.account', [
            'person' => $this->person,
            'auth'   => $this->auth,
            'logs'   => [],
        ]);
    }

    public function postIndex()
    {
        $message = '';

        if ($name = $this->request->input('name')) {
            $this->person->setName($name);
        }

        if ($this->request->input('password1') &&
            $this->request->input('password1') != $this->request->input('current_password')
        ) {
            if (!$this->person->checkPassword($this->request->input('current_password'))) {
                $message = 'Invalid password';
            } elseif ($this->request->input('password1') != $this->request->input('password2')) {
                $message = 'The passwords you entered did not match';
            } else {
                $this->person->setEncryptedPassword($this->auth->hash($this->request->input('password1')));

                Event::fire(new PasswordChanged($this->person, $this->request));
                $message = 'Your password has been updated';
            }
        }

        Person::save($this->person);

        return View::make('boomcms::account.account', [
            'person'  => $this->person,
            'auth'    => $this->auth,
            'logs'    => [],
            'message' => $message,
        ]);
    }
}
