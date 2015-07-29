<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Core\Auth;
use BoomCMS\Http\Controllers\Controller;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

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
        } catch (Auth\PersonLockedException $e) {
            return $this->displayLoginForm([
                'login_error' => Lang::get('boom::auth.locked', ['lock_wait' => $e->getLockWait()]),
            ]);
        }

        $url = Session::get('boomcms.redirect_url');

        return $url ? redirect()->to($url) : redirect()->back();
    }

    protected function displayLoginForm(array $data = [])
    {
        $data['request'] = $this->request;

        return view('boom::account.login', $data);
    }
}
