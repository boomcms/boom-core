<?php

/**
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
