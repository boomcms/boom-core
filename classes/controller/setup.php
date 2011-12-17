<?php defined('SYSPATH') or die('No direct script access.');

/**
* Handles setup stuff in a new sledge install.
*
* @package Controller
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Controller_Setup extends Kohana_Controller
{
	/**
	* Creates the database if it doesn't already exist.
	*
	* @throws Sledge_Exception
	*/
	public function action_database()
	{
		// Check that the datbase doesn't exist. Overwriting databases isn't how we roll.
		try
		{
			$this->db = Database::instance();
			$this->db->connect();
		}
		catch (Database_Exception $e)
		{
			// Is it a database not existing error?
			if (preg_match( '/Unable to connect to (.*) database &quot;(.*)&quot; does not exist/', $e->getMessage(), $matches ))
			{
				$dbname = $matches[2];
				
				exec( "createdb -Uhoopster " . $dbname );
				exec( "psql -Uhoopster " . $dbname . " <  " . MODPATH . "sledge/sql/sledge_full.sql" );

				Request::factory( '/' )->execute();
				exit();
			}
			
			// It's some other error which we don't worry about here.
			throw $e;
		}
		
		// Everything worked? Well that's just not on.
		Request::factory( '/' )->execute();
	}

}

?>