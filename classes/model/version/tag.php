<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Tag extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'tag_v';
	protected $_has_one = array(
		'tag'				=> array( 'model' => 'tag', 'foreign_key' => 'id' ),
	);
}

?>