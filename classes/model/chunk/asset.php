<?php

/**
* Asset chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Asset extends ORM implements iSLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_asset';
	protected $_has_one = array( 
		'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ),
		'asset' => array( 'model' => 'asset', 'foreign_key' => 'id' ),
	);
	
	public function show()
	{
		$v = View::factory( 'site/slots/asset/image' );
		$v->asset = Asset::factory( 'image', $this->asset );
		
		return $v;
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