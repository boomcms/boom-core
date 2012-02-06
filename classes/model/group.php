<?php

/**
* Group Model.
*
* Table name: groups
* The name for this table is pluralised (is that a word?) because 'group' is a reserved word.
* All table names should be plural anyway, but for consistency with the past...
*
* This table is versioned. Full column list in Model_Version_Group.
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	active_vid		****	integer		****	ID of the active version.
******************************************************************
*
* @see Model_Version_Group
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Group extends ORM_Versioned
{
	protected $_table_name = 'groups';

	protected $_belongs_to = array( 
		'version'  => array( 'model' => 'version_group', 'foreign_key' => 'active_vid' ), 
	);
	
	protected $_load_with = array( 'version' );
}


?>