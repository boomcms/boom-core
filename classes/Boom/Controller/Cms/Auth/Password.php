<?php

class Boom_Controller_Cms_Auth_Password extends Controller_Cms_Auth
{
	public function before()
	{
		parent::before();

		if ($this->auth->logged_in())
		{
			$this->redirect('/');
		}
	}
	public function action_begin()
	{
		$this->response->body(View::factory('boom/account/login'));
	}

	public function action_login()
	{
		if ($this->request->method() == Request::POST)
		{
			$person = new Model_Person(array('email' => $this->request->post('email')));

			if (Auth::instance()->login($person, $this->request->post('password')))
			{
				$this->redirect('/');
			}
			else
			{
				if ($person->is_locked())
				{
					die("account locked");
				}
			}
		}

		return;
	}
}