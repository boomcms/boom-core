<?php

abstract class Boom_Controller_Cms_Login extends Controller_Cms_Auth
{
	public $login_methods;
	public $method;

	public function before()
	{
		parent::before();

		$this->login_methods = Kohana::$config->load('auth')->get('login_methods');

		if ( ! in_array($this->method, $this->login_methods))
		{
			throw new HTTP_Exception_500;
		}
	}
}