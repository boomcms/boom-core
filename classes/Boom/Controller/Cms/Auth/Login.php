<?php

abstract class Boom_Controller_Cms_Auth_Login extends Controller_Cms_Auth
{
	/**
	 * @var Session
	 */
	public $session;

	public function before()
	{
		parent::before();

		if ( ! $this->auth->login_method_available($this->method))
		{
			throw new HTTP_Exception_500;
		}

		if ($this->auth->logged_in())
		{
			$this->redirect($this->_get_redirect_url());
		}

		$this->session = Session::instance();
	}

	protected function _login_complete()
	{
		$this->_log_login_success();
		$this->redirect($this->_get_redirect_url(), 303);
	}
}