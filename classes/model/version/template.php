<?php

/**
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Version_Template extends Model_Version {
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'template_v';
	protected $_has_one = array(
		'template'	=> array( 'model' => 'template', 'foreign_key' => 'id' ),
	);
	
	/**
	* Determines whether the template file exists.
	*
	* @return boolean
	*/
	public function file_exists()
	{
		$exists = file_exists( APPPATH . 'views/' . $this->filename . '.php' );

		if (!$exists)
			$exists = file_exists( MODPATH . 'sledge/views/' . $this->filename . '.php' );
		
		return $exists;
	}
}

?>