<?php

/**
*
* Table name: template
* This table is versioned! (Does it really need to be?)
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description		
****	id				****	integer		****	Primary key. auto increment.			
****	active_vid		****	integer		****	The ID of the current version.
****	sequence		****	integer		****	Not sure this is used.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Template extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'template';	
	protected $_belongs_to = array( 
		'version'  => array( 'model' => 'version_template', 'foreign_key' => 'active_vid' ), 
	);
	protected $_has_one = array(
		'page'	   => array( 'model' => 'version_page', 'foreign_key' => 'id' ),
	);
	
	protected $_load_with = array( 'version' );
}

?>