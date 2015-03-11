<?php

use \Boom\Person;

class Controller_Cms_Auth_Login extends Controller_Cms_Auth
{
    /**
	 * @var Session
	 */
    public $session;

    public function before()
    {
        parent::before();

        if ($this->auth->isLoggedIn()) {
            $this->redirect($this->_get_redirect_url());
        }
    }

    public function action_begin()
    {
        if ($this->auth->auto_login()) {
            $this->redirect('/');
        } else {
            $this->_display_login_form();
        }
    }

    public function action_process()
    {
        $provider = new Person\Provider();
        $person = $provider->findByEmail($this->request->post('email'));

        try {
            $this->auth->authenticate($this->request->post('email'), $this->request->post('password'));
        } catch (Exception $e) {
            $this->_log_login_success();
            $this->redirect($this->_get_redirect_url(), 303);
        }
        if ($this->auth->login($person, $this->request->post('password'), $this->request->post('remember') == 1)) {
            $this->_login_complete();
        } else {
            $this->_log_login_failure();

            $error = ($person->isLocked()) ? 'locked' : 'invalid';
            $error_message = Kohana::message('login', "errors.$error");

            if ($person->isLocked()) {
                $lock_wait = \Date::span($person->getLockedUntil());
                $lock_wait = $lock_wait['minutes']." ".Inflector::plural('minute', $lock_wait['minutes']);
                $error_message = str_replace(':lock_wait', $lock_wait, $error_message);
            }

            $this->_display_login_form(['login_error' => $error_message]);
        }
    }
}
