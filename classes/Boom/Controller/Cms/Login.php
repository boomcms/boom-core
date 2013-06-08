<?php

abstract class Boom_Controller_Cms_Login extends Controller_Cms_Auth
{
	public function before()
	{
		parent::before();

		if ( ! $this->auth->login_method_available($this->method))
		{
			throw new HTTP_Exception_500;
		}

		if ($this->auth->logged_in())
		{
			$this->redirect('/');
		}
	}
}