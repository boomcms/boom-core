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
}
