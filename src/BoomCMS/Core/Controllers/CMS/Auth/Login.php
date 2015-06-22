<?php

namespace BoomCMS\Core\Controllers\CMS\Auth;

use BoomCMS\Core\Auth;
use BoomCMS\Core\Controllers\Controller;

use Illuminate\Support\Facades\Lang;

class Login extends Controller
{
    public function showLoginForm()
    {
		return ($this->auth->autoLogin($this->request)) ? redirect('/') : $this->displayLoginForm();
    }

    public function processLogin()
    {
        try {
            $this->auth->authenticate(
                $this->request->input('email'),
                $this->request->input('password'),
                $this->request->input('remember') == 1
            );
        } catch (Auth\PersonNotFoundException $e) {
            return $this->displayLoginForm(['login_error' => Lang::get('Invalid email address or password')]);
        } catch (Auth\PersonSuspendedException $e) {
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
