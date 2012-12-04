<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Sledge_Editor
{
	const EDIT = 1;

	const DISABLED = 2;

	const PREVIEW_ALL = 3;

	/**
	 * Session cache for Editor::live_time() to avoid repeatedly checking the session data.
	 *
	 * @var	integer
	 */
	protected static $_live_time;

	/**
	 * Session cache for Editor::state() to avoid the need to check the session data each time Editor::state() is called.
	 *
	 * @var	integer
	 */
	protected static $_state;

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
	public static function state($state = NULL)
	{
		// Name of the session data key where the state is stored.
		$session_key = 'editor_state';

		if ($state === NULL)
		{
			// Act as a getter.

			// Check the value of Editor::$_state to avoid repeatedly checking the session data.
			if (Editor::$_state === NULL)
			{
				// Determine the default value to pass to Session::get()
				// If the user is logged in then the default is EDIT, if they're not logged in then it should be disabled.
				$default = (Auth::instance()->logged_in())? Editor::EDIT : Editor::DISABLED;

				// Editor::$_state hasn't been set so get the value from the session data.
				Editor::$_state = Session::instance()
					->get($session_key, $default);
			}

			// Return the value of Editor::$_state;
			return Editor::$_state;
		}
		else
		{
			// Act as a setter.

			// Set the new value in Editor::$_state
			Editor::$_state = $state;

			// Save the value to the session data.
			return Session::instance()
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
	public static function live_time($time = NULL)
	{
		// The name of the session data key where the live time is stored.
		$session_key = 'editor_live_time';

		if ($time === NULL)
		{
			// Act as a getter.

			// Has Editor::$_live_time been set?
			if (Editor::$_live_time === NULL)
			{
				// Get the value from the session data.
				Editor::$_live_time = Session::instance()
					->get($session_key, $_SERVER['REQUEST_TIME']);
			}

			// Return the value
			return Editor::$_live_time;
		}
		else
		{
			// Set the time that should be used for viewing pages.

			// Set the value in Editor::$_live_time
			Editor::$_live_time = $time;

			// Store the value in the session data.
			return Session::instance()
				->set($session_key, $time);
		}
	}
}