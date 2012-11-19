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
	 *
	 * @todo Add a Sledge_log class to do this?
	 */
	public static function log($activity, $note = NULL)
	{
		// IP Address of the user.
		$ip = Request::$client_ip;

		// Person ID of the person logged in.
		$person_id = Auth::instance()->get_real_user()->id;

		// Save all the information to the database.
		// We use a prepared statement here so that we can do INSERT DELAYED can help with performance.
		// http://dev.mysql.com/doc/refman/5.0/en/insert-speed.html
		$query = DB::query(Database::INSERT, "insert delayed into activities (remotehost, description, note, person_id, time) values (:remotehost, :activity, :note, :person, :time)")
			->parameters(array(
				':remotehost'	=>	$ip,
				':activity'		=>	$activity,
				':note'		=>	$note,
				':person'		=>	$person_id,
				':time'		=>	$_SERVER['REQUEST_TIME'],
			))
			->execute();
	}
} // End Sledge_Core
