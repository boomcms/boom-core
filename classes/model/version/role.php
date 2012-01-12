<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* 
*/
class Model_Version_Role extends Model_Version {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'role_v';
	
	protected $_has_one = array(
		'role'	=> array( 'model' => 'role', 'foreign_key' => 'id' ),
	);	
}

?>
