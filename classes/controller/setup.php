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
		
		// Check that the config files haven't already been created.
		if (!Kohana::$config->load( 'database.default.connection.sledge' ))
		{
			if (!is_writable( $config_dir ))
			{
				throw new Sledge_Exception( "Config directory $config_dir must be writable" );
				exit;
			}
			
			$group = Arr::get( $_POST, 'group' );
			
			if ($group == 'database')
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
				
				file_put_contents( $config_dir . '/database.php', "<?php \n\n return " . var_export( $config, true ) . ";\n\n?>" );
				
				$v = View::factory( 'setup/config/completed' );
				$v->location = $config_dir;
				echo $v;				
			}
		}
		
		exit;		
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
		$this->request->redirect( '/' );
	}
	
	/**
	* Import Sledge2 database from CSV file.
	*/
	public function action_csv()
	{	
		$DIR = '/home/rob';
		
		// Templates
		$f = fopen( "$DIR/thisishoop2_templates.csv", 'r' );
		
		while( $line = fgets( $f ) )
		{
			list( $person, $time, $id, $rid, $name, $description, $filename ) = explode( '|', $line );
		
			$t = ORM::factory( 'template' );
			$t->audit_person = $person;
			$t->audit_time = $time;
			$t->id = $rid;
			$t->rid = $rid;
			$t->name = $name;
			$t->description = $description;
			$t->filename = trim($filename);
			$t->save();
			
			$t->active_vid = $t->version->id;
			$t->save();
			
			echo "Added template $name<br />";
		}
		
		DB::query( Database::UPDATE, "update template set active_vid = template_v.id from template_v where template_v.rid = template.id;" )->execute();
		
		fclose( $f );
		
		// Pages
		// Templates
		$f = fopen( "$DIR/thisishoop2_pages.csv", 'r' );
		
		while( $line = fgets( $f ) )
		{
			$arr = explode( '|', $line );
			
			if (!$arr[9])
				$arr[9] = null;
		
			$p = ORM::factory( 'page' );
			$p->audit_person = $arr[0];
			$p->audit_time = $arr[1];
			$p->id = (int) $arr[3];
			$p->rid = (int) $arr[3];
			$p->template_id = (int) $arr[4];
			$p->default_child_template_id = (int) $arr[5];
			$p->title = $arr[7];
			$p->visiblefrom_timestamp = $arr[8];
			$p->visibleto_timestamp = $arr[9];
			$p->child_ordering_policy = (int) $arr[11];
			$p->children_hidden_from_leftnav = ($arr[12] == true || $arr[12] == null)? 't' : 'f';
			$p->children_hidden_from_leftnav_cms = ($arr[13] == true || $arr[13] == null)? 't' : 'f';
			$p->version_status = (int) $arr[15];
			$p->approval_process_id = (int) $arr[16];
			$p->ssl_only = ($arr[19] == true || $arr[19] == null)? 't' : 'f';
			$p->pagetype_description = $arr[20];
			$p->visible_in_leftnav = ($arr[21] == true || $arr[21] == null)? 'f' : 't';
			$p->visible_in_leftnav_cms = ($arr[22] == true || $arr[22] == null)? 'f' : 't';
			$p->keywords = $arr[24];
			$p->description = $arr[25];
			$p->pagetype_parent_rid = (int) $arr[27];
			$p->children_pagetype_parent_rid = (int) $arr[28];
			$p->indexed = (bool) $arr[37];
			
			$p->active_vid = $p->version->id;
			$p->save();
			
			try
			{
				$p->save();
			}
			catch (Exception $e)
			{
				print_r( $p );
				throw $e;
			};
			
			$uri = ORM::factory( 'page_uri' );
			$uri->page_id = $p->id;
			$uri->uri = $arr[10];
			$uri->primary_uri = 't';
			$uri->save();
			
			if (!$arr[6])
				$arr[6] = 0;
			
			DB::query( Database::INSERT, "insert into page_mptt (page_id, parent_id, lft, rgt, scope) values (" . $p->id . ", " . $arr[6] . ", " . $arr[33] . ", " . $arr[34] . ", 1)" )->execute();
			
			echo "Added page ", $p->title, ' at ', $arr[10], "<br />";
		}
		
		$mptt = ORM::factory( 'page_mptt' );
		$mptt->rebuild_tree();
		
		DB::query( Database::UPDATE, "update page set active_vid = page_v.id from page_v where page_v.rid = page.id" )->execute();
		
		fclose( $f );	
		
		exit;	
	}

}

?>