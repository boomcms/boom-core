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
	 * Sledge exception handler.
	 * Handles all uncaught exceptions using set_exception_handler()
	 * Registered as exception handler in init.php
	 *
	 * @link 	http://php.net/manual/en/function.set-exception-handler.php
	 * @param 	Exception $e
	 */
	public static function exception_handler($e)
	{
		try
		{
			$request = Request::$initial;

			// Get the exception information
			$code = $e->getCode();
			$message = $e->getMessage();

			// Send the HTTP headers.
			$http_header_status = ($e instanceof HTTP_Exception) ? $code : 500;
			header('Content-Type: '. Kohana_Exception::$error_view_content_type . '; charset=' . Kohana::$charset, TRUE, $http_header_status);

			// If it's a status 500 then log it, unless it's a sledge_exception or validation_exception
			// We don't care too much about anything else.
			if ($http_header_status == 500 AND is_object(Kohana::$log) AND ! $e instanceof Sledge_Exception AND ! $e instanceof Validation_Exception)
			{
				Sledge_Exception::log($e);
			}

			if (is_object($request) AND $request->is_ajax())
			{
				// Error handling for AJAX requests is a little bit special.

				if ($e instanceof ORM_Validation_Exception)
				{
					$messages = array_values($e->errors('models'));
					$response = json_encode(array('type' => 'Validation Error', 'messages' => $messages));
				}
				elseif ($e instanceof Sledge_Exception)
				{
					$response = json_encode(array('type' => 'CMS Error', 'message' => $message));
				}
			}
			else
			{
				// For 403 errors where the person isn't logged in redirect them to the login page.
				if ($e instanceof HTTP_Exception_403)
				{
					if ( ! Auth::instance()->logged_in())
					{
						header("Location: " . URL::site('cms/login', 'https'));
						exit(1);
					}
				}

				// Look for an error page.
				$page = ORM::factory('Page', array('internal_name' => $code));

				if ($page->loaded() AND $page->is_visible() AND $page->is_published())
				{
					// An error page has been created in the CMS for this type of error, show them that.
					$response = Request::factory($page->url())->execute()->body();
				}
				else
				{
					if (Kohana::find_file('views', "sledge/errors/$http_header_status"))
					{
						// Use a pre-set error template.
						$response = View::factory("sledge/errors/$http_header_status");
					}
					else
					{
						// Use a generic 'unable to handle error' template.
						$response = View::factory('sledge/errors/unhandled_404');
					}
				}
			}

			// Output the response body.
			echo $response;
		}
		catch (Exception $e)
		{
			View::factory('sledge/errors/unhandled_500');
		}

		exit(1);
	}

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
