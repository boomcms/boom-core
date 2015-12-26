<?php

namespace BoomCMS\Http\Controllers\CMS\Auth;

use BoomCMS\Core\Auth;
use BoomCMS\Events\Auth\SuccessfulLogin;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class Login extends Controller
{
    use ThrottlesLogins;

    public function showLoginForm()
    {
        return ($this->auth->autoLogin($this->request)) ? redirect('/') : $this->displayLoginForm();
    }

    public function processLogin()
    {
        $request = $this->request;

        try {
            $this->auth->authenticate(
                $this->request->input('email'),
                $this->request->input('password'),
                $this->request->input('remember') == 1
            );
        } catch (Auth\InvalidLoginException $e) {
            $this->incrementLoginAttempts($request);

            if ($this->hasTooManyLoginAttempts($request)) {
                return $this->displayLoginForm([
                    'login_error' => $this->getLockoutErrorMessage($this->lockoutTime())
                ]);
            } else {
                return $this->displayLoginForm(['login_error' => Lang::get('Invalid email address or password')]);
            }
        }

        $this->clearLoginAttempts($request);
        Event::fire(new SuccessfulLogin($this->auth->getPerson(), $this->request));

        $url = Session::get('boomcms.redirect_url');

        return $url ? redirect()->to($url) : redirect()->back();
    }

    protected function displayLoginForm(array $data = [])
    {
        $data['request'] = $this->request;

        return view('boomcms::account.login', $data);
    }

    private function loginUsername()
    {
        return 'email';
    }
}
