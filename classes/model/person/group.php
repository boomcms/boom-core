<?php

/**
* Link table for the one-to-many relationship between people and groups.
* Table name: person_group
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	person_id		****	integer		****	ID from the person table.
****	group_id		****	integer		****	ID from the group table.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Person_Group extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person_group';
	protected $_db_group = 'default';

	
}

?>
