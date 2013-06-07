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

	public function before()
	{
		$this->auth = Auth::instance();
	}

	public function action_logout()
	{
		$this->auth->logout(TRUE);

		// Send the user back to the homepage.
		$this->redirect('/');
	}
}