<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Version_Person extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'person_v';
	protected $_belongs_to = array( 'person' => array( 'model' => 'person', 'foreign_key' => 'active_vid' ) );
	
	
}
?>
