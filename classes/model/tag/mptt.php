<?php

/**
* This model handles tag MPTT records and associated methods.
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Tag_Mptt extends ORM_MPTT {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'tag_mptt';
	protected $_belongs_to = array( 'tag' => array() );	
	
}


?>
