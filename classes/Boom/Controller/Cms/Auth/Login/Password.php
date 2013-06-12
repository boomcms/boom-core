<?php

class Boom_Controller_Cms_Auth_Login_Password extends Controller_Cms_Auth_Login
{
	public $method = 'password';

	public function action_begin()
	{
		if ($this->auth->auto_login())
		{
			$this->redirect('/');
		}
		else
		{
			$this->_display_login_form();
		}
	}

	public function action_process()
	{
		if ( ! Security::check($this->request->post('csrf')))
		{
			throw new HTTP_Exception_500;
		}

		$person = new Model_Person(array('email' => $this->request->post('email')));

		if ($this->auth->login($person, $this->request->post('password'), $this->request->post('remember')))
		{
			$this->_login_complete();
		}
		else
		{
			$this->_log_login_failure();

			$error = ($person->is_locked())? 'locked' : 'invalid';
			$error_message = Kohana::message('login', "errors.$error");

			if ($person->is_locked())
			{
				$lock_wait = $person->get_lock_wait();
				$lock_wait = $lock_wait['minutes']." ".Inflector::plural('minute', $lock_wait['minutes']);
				$error_message = str_replace(':lock_wait', $lock_wait, $error_message);
			}

			$this->_display_login_form(array('login_error' => $error_message));
		}
	}
}