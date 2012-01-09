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
		$this->request->redirect( '/' );
	}
	
	/**
	* Import Sledge2 database from CSV file.
	*/
	public function action_csv()
	{	
		$DIR = '/home/rob';
		
		// Templates
		$f = fopen( '$DIR/thisishoop2_templates.csv' );
		$i = 0;
		
		while( $line = fread( $d ) )
		{
			if ($i > 0)
			{
				list( $person, $time, $id, $rid, $name, $description, $filename ) = explode( ',', $line );
			
				$t = ORM::factory( 'template' );
				$t->audit_person = $person;
				$t->audit_time = $time;
				$t->id = $id;
				$t->name = $name;
				$t->description = $description;
				$t->filename = $filename;
				$t->save();
				
				echo "Added template $name<br />";
			}
			$i++;
		}
		
		close( $f );
		
		// Pages
		// Templates
		$f = fopen( '$DIR/thisishoop2_templates.csv' );
		$i = 0;
		
		while( $line = fread( $d ) )
		{
			if ($i > 0)
			{
				$arr = explode( ',', $line );
			
				$p = ORM::factory( 'page' );
				$p->audit_person = $arr[0];
				$p->audit_time = $arr[1];
				$p->id = $arr[3];
				$p->template_id = $arr[4];
				$p->default_child_template_id = $arr[5];
				$p->title = $arr[7];
				$p->visiblefrom_timestamp = $arr[8];
				$p->visibleto_timestamp = $arr[9];
				$p->child_ordering_policy = $arr[11];
				$p->children_hidden_from_leftnav = $arr[12];
				$p->children_hidden_from_leftnav_cms = $arr[13];
				$p->version_status = $arr[15];
				$p->approval_process_id = $arr[16];
				$p->ssl_only = $arr[19];
				$p->pagetype_description = $arr[20];
				$p->visible_in_leftnav = $arr[35];
				$p->visible_in_leftnav_cms = $arr[36];
				$p->keywords = $arr[24];
				$p->description = $arr[25];
				$p->internal_name = $arr[26];
				$p->pagetype_parent_rid = $arr[27];
				$p->children_pagetype_parent_rid = $arr[28];
				$p->indexed = $arr[37];
				$p->save();
				
				$uri = ORM::factory( 'page_uri' );
				$uri->page_id = $p->id;
				$uri->uri = $arr[10];
				$uri->primary_uri = 't';
				$uri->save();
				
				$mptt = ORM::factory( 'page_mptt' );
				$mptt->page_id = $p->id;
				$mptt->scope = 1;
				$mptt->save();
				
				echo "Added page ", $p->name, ' at ', $arr[10], "<br />";
			}
			
			$i++;
		}
		
		$mptt = ORM::factory( 'page_mptt' );
		$mptt->rebuild_tree;
		
		fclose( $f );	
		
		exit;	
	}

}

?>