<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Class for the CMS editor
 *
 * @package	BoomCMS
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
abstract class Boom_Editor
{
	const EDIT = 1;

	const DISABLED = 2;

	const PREVIEW = 3;

	/**
	 *
	 * @var	Auth
	 */
	protected $_auth;

	/**
	 *
	 * @var	Editor
	 */
	public static $instance;

	/**
	 * Session cache for $this->editor->live_time() to avoid repeatedly checking the session data.
	 *
	 * @var	integer
	 */
	protected $_live_time;

	/**
	 *
	 * @var	Session
	 */
	protected $_session;

	/**
	 * Session cache for Editor::state() to avoid the need to check the session data each time Editor::state() is called.
	 *
	 * @var	integer
	 */
	protected $_state;

	public function __construct(Auth $auth, Session $session)
	{
		// Store the Auth and Session with the object.
		$this->_auth = $auth;
		$this->_session = $session;
	}

	/**
	 * Insert the editor iframe into a block of HTML
	 *
	 * @param	string	$html
	 * @param	string	$page_id
	 * @return	string
	 */
	public function insert($html, $page_id)
	{
		// Find the body tag in the HTML. We need to take into account that the body may have attributes assigned to it in the HTML.
		preg_match("|((</head>)?.*<body[^>]*>)|imsU", $html, $matches);

		if (isset($matches[0]))
		{
			$body_tag = $matches[0];

			// Add the editor iframe to just after the <body> tag.
			$head = View::factory('boom/editor/iframe', array(
				'body_tag'	=>	$body_tag,
				'page_id'	=>	$page_id
			));

			$html = str_replace($body_tag, $head->render(), $html);
		}

		return $html;
	}

	/**
	 * Singleton pattern
	 *
	 * @return Editor
	 */
	public static function instance()
	{
		if (Editor::$instance === NULL)
		{
			Editor::$instance = new Editor(Auth::instance(), Session::instance());
		}

		// Return the editor instance.
		return Editor::$instance;
	}

	/**
	 * Helper method to set / get the 'state' of the CMS editor.
	 * Used to put the editor in a disabled state for previewing pages.
	 *
	 * @uses		Editor::$_state
	 * @uses		Session::get()
	 * @uses		Session::set()
	 * @uses		Auth::logged_in()
	 * @param	integer	$state
	 * @return	mixed
	 */
	public function state($state = NULL)
	{
		// Name of the session data key where the state is stored.
		$session_key = 'editor_state';

		if ($state === NULL)
		{
			// Act as a getter.

			// Check the value of Editor::$_state to avoid repeatedly checking the session data.
			if ($this->_state === NULL)
			{
				// Determine the default value to pass to Session::get()
				// If the user is logged in then the default is EDIT, if they're not logged in then it should be disabled.
				$default = ($this->_auth->logged_in())? Editor::EDIT : Editor::DISABLED;

				// Editor::$_state hasn't been set so get the value from the session data.
				$this->_state = $this->_session
					->get($session_key, $default);
			}

			// Return the value of Editor::$_state;
			return $this->_state;
		}
		else
		{
			// Act as a setter.

			// Set the new value in Editor::$_state
			$this->_state = $state;

			// Save the value to the session data.
			return $this->_session
				->set($session_key, $state);
		}
	}

	/**
	 * The time to use for viewing live pages.
	 * This allows for viewing pages as they were at a certain time in the page.
	 * If no time has been set then the value of $_SERVER['REQUEST_TIME'] is used.
	 *
	 * @uses		Editor::$_live_time
	 * @uses		Session::get()
	 * @uses		Session::set()
	 * @param	integer	$time	Used to set the live time, should be a unix timestamp.
	 * @return	mixed
	 */
	public function live_time($time = NULL)
	{
		// The name of the session data key where the live time is stored.
		$session_key = 'editor_live_time';

		if ($time === NULL)
		{
			// Act as a getter.

			// Has Editor::$_live_time been set?
			if ($this->_live_time === NULL)
			{
				// Get the value from the session data.
				$this->_live_time = Session::instance()
					->get($session_key, $_SERVER['REQUEST_TIME']);
			}

			// Return the value
			return $this->_live_time;
		}
		else
		{
			// Set the time that should be used for viewing pages.

			// Set the value in Editor::$_live_time
			$this->_live_time = $time;

			// Store the value in the session data.
			return $this->_session
				->set($session_key, $time);
		}
	}
}