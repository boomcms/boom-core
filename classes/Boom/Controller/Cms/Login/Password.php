<?php

class Boom_Controller_Cms_Login_Password extends Controller_Cms_Login
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
			$this->response->body(View::factory('boom/account/login'));
		}
	}

	public function action_process()
	{
		extract($this->request->post());

		if ( ! Security::check($csrf))
		{
			throw new HTTP_Exception_500;
		}

		if ($this->request->method() == Request::POST)
		{
			$person = new Model_Person(array('email' => $email));

			if ($this->auth->login($person, $password, $remember))
			{
				$this->_log_login_success();
				$this->redirect('/');
			}
			else
			{
				$this->_log_login_failure();
				if ($person->is_locked())
				{
					die("account locked");
				}
			}
		}
	}
}