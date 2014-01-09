<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Boom base controller.
 * Contains components common to site and cms and controllers.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller extends Controller
{
	/**
	 * The correct content-type header for JSON responses is application/json (http://stackoverflow.com/questions/477816/what-is-the-correct-json-content-type)
	 * 
	 * Unfortunately though IE exists, and IE9 doesn't recognise the application/json type presenting the user with a download confirmation.
	 * 
	 * We therefore need to use text/plain for JSON responses until we stop supporting broken browsers (http://stackoverflow.com/questions/13943439/json-response-download-in-ie710)
	 */
	const JSON_RESPONSE_MIME = 'text/plain';

	/**
	 * The current user.
	 *
	 * @var		Model_Person
	 */
	public $person;

	/**
	 * @var		Auth
	 */
	public $auth;

	/**
	 * @var	Editor
	 */
	public $editor;

	/**
	 * @var Session
	 */
	public $session;

	/**
	 *
	 * @var View
	 */
	public $template;

	public function before()
	{
		$this->auth = Auth::instance();
		$this->session = Session::instance();

		$this->_save_last_url();

		// Require the user to be logged in if the site isn't live.
		if ($this->request->is_initial() AND ! (Kohana::$environment == Kohana::PRODUCTION OR $this->auth->logged_in()))
		{
			throw new HTTP_Exception_401;
		}

		// Who are we?
		$this->person = $this->auth->get_user();

		// Get an editor instance
		$this->editor = Editor::instance();
	}

	/**
	 * Checks whether the current user is authorized to perform a particular action.
	 *
	 * Throws a HTTP_Exception_403 error if the user hasn't been given the required role.
	 *
	 * @uses	Auth::logged_in()
	 * @param string $role
	 * @param Model_Page $page
	 */
	public function authorization($role, Model_Page $page = NULL)
	{
		if ( ! $this->auth->logged_in())
		{
			throw new HTTP_Exception_401;
		}

		if ( ! $this->auth->logged_in($role, $page))
		{
			throw new HTTP_Exception_403;
		}
	}

	/**
	 * Log an action in the CMS log
	 *
	 * @param string $activity
	 */
	public function log($activity)
	{
		// Add an item to the log table with the relevant details
		ORM::factory('Log')
			->values(array(
				'ip'			=>	Request::$client_ip,
				'activity'		=>	$activity,
				'person_id'	=>	$this->person->id,
			))
			->create();
	}

	public function after()
	{
		if ($this->template instanceof View AND ! $this->response->body())
		{
			parent::after();

			// Set some variables.
			// TODO: remove these when sure that they're no longer needed.
			View::bind_global('person', $this->person);
			View::bind_global('auth', $this->auth);

			$this->response->body($this->template);
		}

		if ($this->response->status() >= 400)
		{
			$logger = new RecentUrlLogger($this->session);
			$logger->remove_url($this->request->url());
		}

		if ($this->auth->logged_in())
		{
			$this->response->headers('Cache-Control', 'private');
		}
	}

	protected function _save_last_url()
	{
		$logger = new RecentUrlLogger($this->session);
		$logger->add_url(Request::initial()->url());
	}

	protected function _csrf_check()
	{
		if ( ! Security::check($this->request->post('csrf')))
		{
			throw new HTTP_Exception_500;
		}
	}
}