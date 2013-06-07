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
	/**
	 * Logout controller
	 *
	 * @uses Auth::logout()
	 */
	public function action_logout()
	{
		// Use [Auth::logout()] to do the logging out.
		Auth::instance()->logout(TRUE);

		// Send the user back to the homepage.
		$this->redirect('/');
	}
}