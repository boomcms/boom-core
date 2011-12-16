<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
* @todo Work out which methods we actually need from hoopbasepagemodel and implement them nicely. Then just extend ORM 
*
*/
class Model_Template extends ORM {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'template';
	protected $_has_one = array( 
		'version'	=> array( 'model' => 'version_template', 'foreign_key' => 'id' ),
	);
	protected $_belongs_to = array( 'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'template_id' ) );
	protected $_load_with = array( 'version' );	
	
	/**
	* Determines whether the template file exists.
	*
	* @return boolean
	*/
	public function fileExists()
	{
		$exists = file_exists( APPPATH . 'views/' . $this->version->filename . '.php' );

		if (!$exists)
			$exists = file_exists( MODPATH . 'sledge/views/' . $this->version->filename . '.php' );
		
		return $exists;
	}
}

?>