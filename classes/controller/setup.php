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
	* Creates runtime configuration files
	*/
	public function action_config()
	{
		$config_dir = APPPATH . 'config';
		
		if (!is_writable( $config_dir ))
		{
			throw new Sledge_Exception( "Config directory $config_dir must be writable" );
			return;
		}
		
		$group = Arr::get( $_POST, 'group' );
		
		if ($group == 'database' && !Kohana::$config->load( 'database.default.connection.sledge' ))
		{
			$config = array(
				'default' => array
				(
					'type'		 => 'mysql',
					'connection' => array
					(	
						'hostname'	=> Arr::get( $_POST, 'hostname' ),
						'username'	=> Arr::get( $_POST, 'username' ),
						'password'	=> Arr::get( $_POST, 'password' ),
						'persistent'=> true,
						'database'	=> Arr::get( $_POST, 'dbname' ),
						'sledge'	=> true,
					)
				),
			);
		}
		else if ($group == 'sledge' && !Kohana::$config->load( 'sledge.environment' ))
		{
			$config = array(
				'environment'	=> Arr::get( $_POST, 'environment' ),
			);
		}				
			
		file_put_contents( $config_dir . '/' . $group . '.php', "<?php \n\n return " . var_export( $config, true ) . ";\n\n?>" );
		
		$this->request->redirect( '/' );		
	}
	
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
			if (preg_match( "/Unknown database '(.*)'/", $e->getMessage(), $matches ))
			{
				$dbname = $matches[1];
				
				exec( "mysqladmin -u root create " . $dbname );
				exec( "mysql -u root " . $dbname . " <  " . MODPATH . "sledge/sql/sledge_full.mysql.sql" );

				$this->request->redirect( '/' );
			}
			
			// It's some other error which we don't worry about here.
			throw $e;
		}
		
		// Everything worked? Well that's just not on.
		$this->request->redirect( '/' );
	}
}

?>