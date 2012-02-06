<?php

/**
* The version table for groups.
*
* Table name: group_v
* 
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description		
****	id				****	integer		****	Primary key. auto increment.			
****	rid				****	integer		****	ID of the group that this version belongs to.
****	name			****	string		****	The name of the group.
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
class Model_Version_Group extends Model_Version {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'group_v';
	
	protected $_has_one = array(
		'group'	=> array( 'model' => 'group', 'foreign_key' => 'id' ),
	);	
}

?>
