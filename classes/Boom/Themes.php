<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Functions for theming the CMS interface.
*
* @package	BoomCMS
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Themes
{
	/**
	 * Sets the default theme
	 *
	 * @var	string
	 */
	public static $default = 'smoothness';

	/**
	 * Returns the theme which is currently in use.
	 */
	public static function current()
	{
		$auth = Auth::instance();

		if ($auth->logged_in())
		{
			// Get the user specified theme.
			$theme = $auth->get_user()->theme;

			// If the user's theme is NULL then they haven't set a theme so use the default.
			if ($theme != NULL)
			{
				return $theme;
			}
		}

		return Themes::$default;
	}

	/**
	* Returns an array of available themes.
	*
	* @return array
	*/
	public static function find()
	{
		$files = Kohana::list_files('media/boom/css/themes');

		foreach ($files as $dir => $files)
		{
			// $dir is a directory and there's a jquery-ui.css file in it.
			if (is_array($files) AND isset($files[$dir . '/jquery-ui.css']))
			{
				// Get the last part of the directory name and remove /ui- to get the theme name.
				$theme = substr(strrchr($dir, '/'), 1);
				$themes[$theme] = ucfirst($theme);
			}
		}

		return $themes;
	}
} // End Boom_Themes