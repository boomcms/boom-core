<?php
/**
* Model for the text chunk table.
*
* Table name: chunk_text
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	text			****	string		****	The content of the text chunk.
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
class Model_Chunk_Text extends ORM implements Interface_Slot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_text';
	protected $_primary_key = 'chunk_id';
	
	/**
	* Filters for the versioned person columns
	* @see http://kohanaframework.org/3.2/guide/orm/filters
	*/
	public function filters()
	{
	    return array(
			'text' => array(
				array( 'html_entity_decode' ),
				array( 'urldecode' ),
			),
	    );
	}
	
	public function show()
	{
		if ($this->loaded())
		{
			// Fix image links.
			$text = $this->text;
			$text = preg_replace( "|hoopdb://image/(\d+)|", "/asset/view/$1", $text );
			return $text;
		}
		else
		{
			return '';
		}
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
