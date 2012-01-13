<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Work out which methods we actually need from hoopbasepagemodel and implement them nicely. Then just extend ORM 
*
*/
class Model_Tag extends ORM_Versioned {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'tag';
	protected $_has_one = array( 
		'mptt'		=> array( 'model' => 'tag_mptt' ),
	);

	protected $_belongs_to = array(
		'version'  => array( 'model' => 'version_tag', 'foreign_key' => 'active_vid' ), 
	);
	
	protected $_load_with = array('version');
}


?>
