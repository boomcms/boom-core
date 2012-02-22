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
	
	public function action_import()
	{
		// Import pages.
		$old = Database::instance( 'old' );
		$new = Database::instance();
		
		$new->query( Database::DELETE, "truncate page" );
		$new->query( Database::DELETE, "truncate page_v" );
		$new->query( Database::DELETE, "truncate page_uri" );
		$new->query( Database::DELETE, "truncate page_mptt" );
		$new->query( Database::DELETE, "truncate chunk_page" );
		$new->query( Database::DELETE, "truncate chunk" );
		$new->query( Database::DELETE, "truncate chunk_text" );
		$new->query( Database::DELETE, "truncate chunk_feature" );
		$new->query( Database::DELETE, "truncate chunk_asset" );
		$new->query( Database::DELETE, "truncate chunk_linkset" );
		$new->query( Database::DELETE, "truncate linksetlinks" );
		$new->query( Database::DELETE, "truncate chunk_slideshow" );
		$new->query( Database::DELETE, "truncate slideshowimages" );
		$new->query( Database::DELETE, "truncate asset" );
		$new->query( Database::DELETE, "truncate asset_v" );
		$new->query( Database::DELETE, "truncate template" );
		$new->query( Database::DELETE, "truncate template_v" );
		$new->query( Database::DELETE, "truncate tag" );
		$new->query( Database::DELETE, "truncate tag_v" );
		$new->query( Database::DELETE, "truncate tag_mptt" );
		$new->query( Database::DELETE, "truncate tagged_objects" );
		
		// Templates.
		$templates = $old->query( Database::SELECT, "select deleted, template_v.* from template inner join template_v on active_vid = template_v.id" );
		
		foreach( $templates as $t )
		{
			$template = ORM::factory( 'template' );
			$template->id = $t['rid'];
			$template->filename = str_replace( "tpl_", "", $t['filename'] );
			$template->name = $t['name'];
			$template->description = $t['description'];
			$template->deleted = ($t['deleted'] == 't')? true : false;
			$template->save();					
		}		
		
		// Assets.
		$assets = $old->query( Database::SELECT, "select *, asset_v.id as vid from asset inner join asset_v on asset.active_vid = asset_v.id" );
		
		foreach( $assets as $a )
		{
			$asset = ORM::factory( 'asset' );
			$asset->id = $a['rid'];
			$asset->title = $a['title'];
			$asset->width = $a['width'];
			$asset->height = $a['height'];
			$asset->filename = $a['filename'];
			$asset->crop_start_x = $a['crop_start_x'];
			$asset->crop_start_y = $a['crop_start_y'];
			$asset->crop_end_x = $a['crop_end_x'];
			$asset->crop_end_y = $a['crop_end_y'];
			$asset->status = 2;
			$asset->type = 'image';
			$asset->save();	
			
			$new->query( Database::UPDATE, "update asset inner join asset_v on active_vid = asset_v.id set active_vid = " . $a['vid'] . ", asset_v.id = " . $a['vid'] . " where asset.id = " . $a['rid'] );				
		}
			
		// Pages.
		$homepage = $old->query( Database::SELECT, "select * from cms_page where uri = ''" )->as_array();
		
		$page = Import::import_page( $homepage[0], $old );
				
		$hmptt = ORM::factory( 'page_mptt' )->values( array( 'page_id' => $page->id ))->create();
		$hmptt->make_root();
		
		// Home page slots.
		Import::chunk_text( $old, $homepage[0]['vid'], $page );
		Import::chunk_feature( $old, $homepage[0]['vid'], $page );
		Import::chunk_asset( $old, $homepage[0]['vid'], $page );
		
		// Descend down the tree.
		Import::child_pages( $old, $homepage[0]['rid'], $page, $hmptt );
		
		// Import page's which aren't part of the main tree - 404 etc.
		$other = $old->query( Database::SELECT, "select * from cms_page where uri is null and title = 'Other'" )->as_array();
		$other[0]['hidden_from_leftnav'] = 't';
		$other[0]['hidden_from_leftnav_cms'] = 't';
		
		$page = Import::import_page( $other[0], $old );
				
		$mptt = ORM::factory( 'page_mptt' );
		$mptt->page_id = $page->id;
		$mptt->insert_as_last_child( $hmptt );
		
		// Home page slots.
		Import::chunk_text( $old, $other[0]['vid'], $page );
		Import::chunk_feature( $old, $other[0]['vid'], $page );
		Import::chunk_asset( $old, $other[0]['vid'], $page );
		
		// Descend down the tree.
		Import::child_pages( $old, $other[0]['rid'], $page, $mptt );		
		
		$new->query( Database::UPDATE, "update page set published_vid = active_vid" );
		
		// Import tags.
		Import::child_tags( $old );
		
		Import::rss_pages( $old );
	}
}

?>