<?php defined('SYSPATH') OR die('No direct script access.');

/**
* Sledge helper methods
*
* @package Sledge
* @category Base
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Sledge_Core
{
	const TEMPLATE_DIR = 'site/templates/';

	/**
	* Add something to the activity log.
	* This method is called from controllers to add an item to the activity log (stored in the db activity log table).
	* Unfortunately we can't integrate this into Kohana's logging mechanism as we need to store associated data (e.g. IP, user ID)
	* as well as the log message, which isn't supported, and Kohana doesn't allow extending the Log class so that we can add this functionality.
	*/
	public static function log($activity, $note = NULL)
	{
		// IP Address of the user.
		$ip = Request::$client_ip;

		// Person ID of the person logged in.
		$person_id = Auth::instance()->get_real_user()->id;

		// Get the current time.
		$time = time();

		// Save all the information to the database.
		// We use a prepared statement here so that we can do INSERT DELAYED can help with performance.
		// http://dev.mysql.com/doc/refman/5.0/en/insert-speed.html
		$query = DB::query(Database::INSERT, "insert delayed into activities (remotehost, description, note, person, time) values (:remotehost, :activity, :note, :person, :time)")
			->parameters(array(
				':remotehost'	=>	$ip,
				':activity'		=>	$activity,
				':note'		=>	$note,
				':person'		=>	$person_id,
				':time'		=>	$time,
			))
			->execute();
	}

	/**
	 * This function controls menu generation.
	 *
	 * @param	string	$section	The name of the menu to be generated.
	 * @return 	string
	 */
	public static function menu($section)
	{
		// Get all the menu items for the given section.
		$menu_items = Kohana::$config->load('menu')->$section;

		// Array of items we're going to include for this menu.
		$menu = array();

		foreach ($menu_items as $details)
		{
			// Include the item in the menu if a required action isn't given or the current user is allowed to perform the action.
			if ( ! isset($details['action']) OR Auth::instance()->logged_in($details['action']))
			{
				// If no position is set then use a high number to move it to the end.
				$details['priority'] = isset($details['priority'])? $details['priority'] : 1000;

				// Add the item to the memu.
				$menu[] = $details;
			}
		}

		// Sort the menu items by priority.
		usort($menu, function($a, $b){
			return $a['priority'] - $b['priority'];
		});

		// Check that we've got some items to add to the menu.
		if ( ! empty($menu))
		{
			// If there's a template for this section then use that, otherwise use a generic template.
			$template = (Kohana::find_file('views', "sledge/menu/$section"))? "sledge/menu/$section" : "sledge/menu/default";
			$template = View::factory($template);
			$template->menu = $menu;

			return $template->render();
		}

		return "";
	}

	/**
	* Returns an array of available themes.
	*
	* @return array
	*/
	public static function themes()
	{
		if (Kohana::$environment !== Kohana::PRODUCTION OR ! $themes = Cache::instance()->get('themes'))
		{
			$files = Kohana::list_files('media/sledge/css/themes');

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

			Cache::instance()->set('themes', $themes, 3600);
		}

		return $themes;
	}
} // End Sledge_Core
