<?php

class Boom_Controller_Cms_Recover extends Controller_Auth
{
	public function before()
	{
		parent::before();

		if ( ! $this->auth->login_method_available('password'))
		{
			throw new HTTP_Exception_404;
		}
	}
}