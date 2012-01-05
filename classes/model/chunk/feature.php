<?php

/**
* Feature chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Feature extends ORM implements iSLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_feature';
	protected $_has_one = array( 'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ));
	
	public function show()
	{
		if ($this->loaded())
		{
			// Do feature stuff
		}
		else
			return 'Default Feature';		
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