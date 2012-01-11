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
	protected $_load_with = array( 'chunk' );
	
	public function show()
	{
		if ($this->loaded())
			return $this->text;
		else
			return '';
	}
	
	public function show_default()
	{
		return 'Click on me to add some text here.';
	}
	
	public function get_slotname()
	{
		return $this->chunk->slotname;
	}
	
	public function getTitle()
	{
		
		
		
	}
	
	public function __toString()
	{
		return $this->show();
	}
	
}

?>