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
	protected $_belongs_to = array
	( 
		'version_page' => array( 'model' => 'version_page', 'foreign_key' => 'template_id' ),
	);
	protected $_has_many = array
	(
		'versions' => array( 'model' => 'version_template', 'foreign_key' => 'id', 'far_key' => 'template_id' )
	);
	protected $_load_with = array( 'version' );	
	
	private $_version;
	
	public function version()
	{
		if ($this->_version === null)
		{
		 	$this->_version = $this->versions->where( 'id' , '=', $this->active_vid )->find();
		}
		
		return $this->_version;
	}
}

?>