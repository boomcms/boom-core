<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Sledge_Editor
{
	const EDIT = 1;

	const PREVIEW_PUBLISHED = 2;

	const PREVIEW_ALL = 3;

	/**
	* Helper method to set / get the 'state' of the CMS editor.
	* Used to put the editor in a disabled state for previewing pages.
	*
	* @param string $state
	* @return string
	*/
	public static function state($state = NULL)
	{
		if ($state === NULL)
		{
			// $state is null, return the current editor state.
			return Session::instance()->get("editor_state", static::EDIT);
		}
		else
		{
			$state = constant("Editor::" . strtoupper($state));
			return Session::instance()->set("editor_state", $state);
		}
	}

	/**
	 * The time to use for viewing live pages.
	 * This allows for viewing pages as they were at a certain time in the page.
	 * If no time has been set then the value of $_SERVER['REQUEST_TIME'] is used.
	 *
	 * @param	integer	$time	Used to set the live time, should be a unix timestamp.
	 */
	public static function live_time($time = NULL)
	{
		// The name of the session data key where the live time is stored.
		$session_key = 'editor_live_time';

		if ($time === NULL)
		{
			// Act as a getter.
			return Session::instance()
				->get($session_key, $_SERVER['REQUEST_TIME']);
		}
		else
		{
			// Set the time that should be used for viewing pages.
			return Session::instance()
				->get($session_key, $time);
		}
	}
}