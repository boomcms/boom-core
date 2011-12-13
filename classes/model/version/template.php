<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Template extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'template_v';
	protected $_belongs_to = array( 'template' => array( 'model' => 'template', 'foreign_key' => 'active_vid' ) );
}

?>