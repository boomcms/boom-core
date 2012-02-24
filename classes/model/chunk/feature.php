<?php
/**
* Model for the feature chunk table.
*
* Table name: chunk_feature
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	id				****	integer		****	Primary key, auto increment
****	target_page_id	****	integer		****	The page ID being featured.
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
class Model_Chunk_Feature extends ORM implements Interface_Slot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_feature';
	protected $_primary_key = 'chunk_id';
	
	protected $_belongs_to = array(
		'target' => array( 'model' => 'page', 'foreign_key' => 'target_page_id' ),
	);
	
	public function show ( $template = null )
	{
		// Loaded check was disabled for inserting a new feature chunk.
		//if ($this->loaded())
		//{
			var_dump( $template );
			
			if ($template == null)
			{
				$template = 'main';
			}
						
			$v = View::factory( "site/slots/feature/$template" );
					
			$v->url = $this->target->url();
			$v->title = $this->target->title;
			$v->text = $this->target->get_slot( 'text', 'standfirst' )->show();
						
			if ($this->target->feature_image)
			{
				$v->image_id = $this->target->feature_image;
			}
			
			return $v;	
	//	}	
	}
	
	public function show_default( $template = 'main' )
	{
		$v = View::factory( "site/slots/feature/$template" );
		$v->url = '';
		$v->title = 'Default Feature';
		$v->text = 'Click on me to add a feature box here.';	
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
