<?php

namespace BoomCMS\Http\Controllers\Auth;

use BoomCMS\Auth\Hasher;
use BoomCMS\Events\Auth\PasswordChanged;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class Account extends Controller
{
    /**
     * @var string
     */
    protected $role = 'manageAccount';

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getIndex()
    {
        return view('boomcms::account.account', [
            'person' => auth()->user(),
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
                $person->setEncryptedPassword((new Hasher())->make($this->request->input('password1')));

                Event::fire(new PasswordChanged($person, $this->request));
                $message = 'Your password has been updated';
            }
        }

        Person::save($person);

        return view('boomcms::account.account', [
            'person'  => $person,
            'logs'    => [],
            'message' => $message,
        ]);
    }
}
