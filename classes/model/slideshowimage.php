<?php

/**
* Slideshow Image model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Slideshowimage extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'slideshowimages';	
	
	protected $_has_one = array(
		'asset'	=> array( 'model' => 'asset', 'foreign_key' => 'id')
	);
}

?>