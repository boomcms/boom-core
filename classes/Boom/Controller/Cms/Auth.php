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
	 *
	 * @var Auth
	 */
	public $auth;

	/**
	 *
	 * @var int
	 */
	public $method;

	public function before()
	{
		$this->auth = Auth::instance();
	}

	public function action_logout()
	{
		// This needs to happen before we log the user out, or we don't be able to log who logged out.
		$this->_log_logout();

		$this->auth->logout(TRUE);

		// Send the user back to the homepage.
		$this->redirect('/');
	}

	protected function _log_login_success()
	{
		$this->_log_action(1);
	}

	protected function _log_login_failure()
	{
		$this->_log_action(0);
	}

	protected function _log_logout()
	{
		$this->_log_action(-1);
	}

	private function _log_action($action)
	{
		ORM::factory('AuthLog')
			->values(array(
				'person_id' => $this->auth->get_user()->id,
				'action' => $action,
				'method' => $this->method,
				'ip' => ip2long(Request::$client_ip),
				'user_agent' => $this->request->user_agent(),
			))
			->create();
	}
}