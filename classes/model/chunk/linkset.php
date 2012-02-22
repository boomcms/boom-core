<?php
/**
* Model for the linkset chunk table.
*
* Table name: chunk_linkset
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	title			****	string		****	The linkset title.
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
class Model_Chunk_Linkset extends ORM implements Interface_SLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_linkset';
	protected $_primary_key = 'chunk_id';
	protected $_has_many = array(
		'links' => array( 'model' => 'linksetlink' ),
	);
	protected $_load_with = array( 'links' );
	
	public function show( $template = 'quicklinks' )
	{
		if ($this->loaded())
		{
			$v = View::factory( "site/slots/linkset/$template" );
			
			$v->title = $this->title;
			$v->links = $this->links->find_all();
			
			return $v;
		}
	}
	
	public function show_default( $template = 'quicklinks' )
	{
		$v = View::factory( "site/slots/linkset/$template" );

		$link = ORM::factory( 'linksetlink' );
		$link->title = 'Add Link';
		$link->url = '#';
		
		// Set some default values.
		$v->links = array( $link );
		$v->title = 'Default Linkset';
		
		return $v;		
	}
	
	/**
	* Copy the slot
	*
	* @todo Move this to a ORM_Slot class - it's the same for all slots
	*/
	public function copy()
	{
		$new = parent::copy();
		$new->chunk = $this->chunk->copy();
		
		return $new;
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
