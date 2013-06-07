<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Authentication controller which uses OpenID
 *
 * @package	BoomCMS/People
 * @category	Controllers
 * @author	Rob Taylor
 */
class Boom_Controller_Cms_Auth extends Controller
{
	public $auth;
	public $login_methods;

	public function before()
	{
		$this->auth = Auth::instance();
		$this->login_methods = Kohana::$config->load('auth')->get('login_methods');

		if ( ! in_array($this->method, $this->login_methods))
		{
			throw new HTTP_Expection_500;
		}
	}

	public function action_logout()
	{
		$this->auth->logout(TRUE);

		// Send the user back to the homepage.
		$this->redirect('/');
	}
}