<?php

/**
* Text chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Text extends ORM implements iSLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_text';
	protected $_has_one = array( 'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ));
	
	public function show()
	{
		return 'Text chunk';
		
	}
	
	public function getSlotname()
	{
		return $this->chunk->slotname;
	}
	
	public function getTitle()
	{
		
		
		
	}
	
}

?>