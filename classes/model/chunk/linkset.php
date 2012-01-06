<?php

/**
* Linkset chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Linkset extends ORM implements iSLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_linkset';
	protected $_has_one = array( 
		'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ),
	);
	protected $_has_many = array(
		'links' => array( 'model' => 'linksetlinks' ),
	);
	protected $_load_with = array( 'links' );
	
	
	public function show()
	{
		if ($this->loaded())
		{
			return View::factory( 'site/slots/linkset' )->bind( 'linkset', $this );
		}
		else
			return 'Default Linkset';		
	}
	
	public function get_slotname()
	{
		return $this->chunk->slotname;
	}
	
	public function getTitle()
	{
		
		
		
	}	
}

?>