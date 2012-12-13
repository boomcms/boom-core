<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Sledge base controller.
 * Contains components common to site and cms and controllers.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller extends Controller
{
	/**
	 * The current user.
	 *
	 * @var		Model_Person
	 */
	public $person;

	/**
	 * Holds the auth instance.
	 *
	 * @var		Auth
	 */
	public $auth;

	/**
	 * Holds the editor instance
	 *
	 * @var	Editor
	 */
	public $editor;

	protected $template;

	public function before()
	{
		// Assign the auth instance to a property so that permissions can be checked within the controller without repeatd calles to Auth::instnace()
		$this->auth = Auth::instance();

		// Require the user to be logged in if the site isn't live.
		if (Kohana::$environment != Kohana::PRODUCTION AND ! $this->auth->logged_in())
		{
			throw new HTTP_Exception_403;
		}

		// Who are we?
		$this->person = $this->auth->get_user();

		// Get an editor instance
		$this->editor = Editor::instance();
	}

	/**
	 * Checks whether the current user is authorized to perform a particular action.
	 * Throws a HTTP_Exception_403 error if the user hasn't been given the required role.
	 *
	 * @param	string	$role
	 * @param	Model_Page	$page
	 * @throws	HTTP_Exception_403
	 */
	protected function _authorization($role, Model_Page $page = NULL)
	{
		if ( ! $this->auth->logged_in($role, $page))
		{
			throw new HTTP_Exception_403;
		}
	}

	/**
	 * Log an action in the CMS log
	 *
	 * @param	type	$activity
	 */
	protected function _log($activity)
	{
		// Add an item to the log table with the relevant details
		ORM::factory('Log')
			->values(array(
				'ip'			=>	Request::$client_ip,
				'activity'		=>	$activity,
				'person_id'	=>	$this->auth->get_real_user()->id,
				'time'		=>	$_SERVER['REQUEST_TIME'],
			))
			->create();
	}

	public function after()
	{
		if ($this->template instanceof View AND ! $this->response->body())
		{
			parent::after();

			// Set some variables.
			View::bind_global('person', $this->person);
			View::bind_global('auth', $this->auth);

			// Show the template.
			$this->response
				->body($this->template);
		}

		// Make cache private if the user is logged in.
		if ($this->auth->logged_in())
		{
			$this->response
				->headers('Cache-Control', 'private');
		}
	}
}