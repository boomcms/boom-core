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
			$standfirst = ORM::factory( 'chunk_text' )
							->join( 'chunk' )
							->on( 'chunk.active_vid', '=', 'chunk_text.id' )
							->join( 'chunk_page' )
							->on( 'chunk_page.chunk_id', '=', 'chunk.id' )
							->where( 'slotname', '=', 'standfirst' )
							->where( 'chunk_page.page_id', '=', $this->target_page_id )
							->find();
			
			if ($standfirst->loaded())
				return $standfirst->show();
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