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

	/**
	 *
	 * @var View
	 */
	public $template;

	public function before()
	{
		// Assign the auth instance to a property so that permissions can be checked within the controller without repeatd calles to Auth::instnace()
		$this->auth = Auth::instance();

		// Require the user to be logged in if the site isn't live.
		if ( ! (Kohana::$environment == Kohana::PRODUCTION OR $this->auth->logged_in() OR ! $this->request->is_external()))
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
	 * @throws HTTP_Exception_403
	 */
	public function authorization($role, Model_Page $page = NULL)
	{
		// Is the current user logged in?
		if ( ! $this->auth->logged_in())
		{
			throw new HTTP_Exception_401;
		}

		// Can the current user perform the role at the given page?
		if ( ! $this->auth->logged_in($role, $page))
		{
			// No they can't, tell them to leave us alone.
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