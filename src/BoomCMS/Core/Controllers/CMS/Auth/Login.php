<?php

namespace BoomCMS\Core\Controllers\CMS\Auth;

use BoomCMS\Core\Controllers\Controller;
use BoomCMS\Core\Person;

use Cartalyst\Sentry\Users\UserNotFoundException;
use Illuminate\Support\Facades\Lang;

class Login extends Controller
{
    public function showLoginForm()
    {
        if ($this->auth->auto_login()) {
            $this->request->redirect('/');
        } else {
            return $this->displayLoginForm();
        }
    }

    public function processLogin(Person\Provider $provider)
    {
        try {
            $this->auth->authenticate($this->request->input('email'), $this->request->input('password'));
        } catch (UserNotFoundException $e) {
            return $this->displayLoginForm(['login_error' => Lang::get('Invalid email address or password')]);
        } catch (PersonSuspendedException $e) {
            return $this->displayLoginForm([
                'login_error' => Lang::get('Your account has been locked due to too many unsuccessful login attempts. Please try again in :lock_wait', ['lock_wait' => $e->getLockWait()]),
            ]);
        }

        return redirect()->back();
    }

    protected function displayLoginForm(array $data = [])
    {
        $data['request'] = $this->request;

        return view('boom::account.login', $data);
    }
}
