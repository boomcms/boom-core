<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Events\Auth\PasswordChanged;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class Account extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getIndex()
    {
        return view('boomcms::account.account', [
            'person' => $this->person,
            'logs'   => [],
        ]);
    }

    public function postIndex()
    {
        $message = '';
        $person = auth()->user();

        if ($name = $this->request->input('name')) {
            $person->setName($name);
        }

        if ($this->request->input('password1') &&
            $this->request->input('password1') != $this->request->input('current_password')
        ) {
            if (!$person->checkPassword($this->request->input('current_password'))) {
                $message = 'Invalid password';
            } elseif ($this->request->input('password1') != $this->request->input('password2')) {
                $message = 'The passwords you entered did not match';
            } else {
                $person->setEncryptedPassword(auth()->hash($this->request->input('password1')));

                Event::fire(new PasswordChanged($person, $this->request));
                $message = 'Your password has been updated';
            }
        }

        Person::save($person);

        return view('boomcms::account.account', [
            'person'  => $this->person,
            'logs'    => [],
            'message' => $message,
        ]);
    }
}
