<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Page_Uri extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'page_uri_v';
	protected $_belongs_to = array( 'page_uri' => array( 'foreign_key' => 'active_vid' ) );
	//protected $_has_one = array( 'page' => array( 'foreign_key' => 'id' ) );
}

?>