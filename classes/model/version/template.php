<?php

/**
*
* The version table for templates.
*
* Table name: template_v
* 
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description		
****	id				****	integer		****	Primary key. auto increment.			
****	rid				****	integer		****	ID of the template that this version belongs to.
****	name			****	string		****	The name of the template.
****	description		****	string		****	A description of the template. Shown in the template manager.
****	filename		****	string		****	The template filename.
****	visible			****	boolean		****	Whether the template is visible. Not sure this is needed.
****	audit_person	****	integer		****	Person ID of the user who created the version.
****	audit_time		****	integer		****	Unix timestamp of when the version was created.
****	deleted			****	boolean		****	Whether the group has been deleted.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Template extends Model_Version {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'template_v';
	protected $_has_one = array(
		'template'	=> array( 'model' => 'template', 'foreign_key' => 'id' ),
	);
	
	/**
	* Determines whether the template file exists.
	*
	* @return boolean
	*/
	public function file_exists()
	{
		$exists = file_exists( APPPATH . 'views/' . $this->filename . '.php' );

		if (!$exists)
			$exists = file_exists( MODPATH . 'sledge/views/' . $this->filename . '.php' );
		
		return $exists;
	}
}

?>