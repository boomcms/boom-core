<?php
/**
* Model for the asset chunk table.
*
* Table name: chunk_asset
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	asset_id		****	integer		****	ID of the asset being slotted.
****	text			****	string		****	Not sure why asset slots need text.
****	audit_person	****	integer		****	ID of the person who edited the chunk.
****	audit_time		****	integer		****	Unix timestamp of when the chunk was edited.
****	deleted			****	boolean		****	Whether the chunk has been deleted.
******************************************************************
*
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Asset extends ORM implements Interface_SLot
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
		if ($this->loaded())
		{
			$v = View::factory( 'site/slots/asset/image' );
			$v->asset = Asset::factory( 'image', $this->asset_id );
		
			return $v;
		}
	}
	
	public function show_default()
	{
		return "<em>(Click here to add an image.)</em>";
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