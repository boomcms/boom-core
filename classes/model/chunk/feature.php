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
	protected $_has_one = array( 
		'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ),
	);
	
	
	public function show()
	{
		if ($this->loaded())
		{
			$v = View::factory( 'site/slots/feature' );
		
			$target = ORM::factory( 'page', $this->target_page_id );
			$v->url = $target->url();
			$v->title = $target->title;
			$v->text = $target->get_slot( 'text', 'standfirst' );
			
			return $v;	
		}	
	}
	
	public function show_default()
	{
		$v = View::factory( 'site/slots/feature' );
		$v->url = '';
		$v->title = 'Default Feature';
		$v->text = 'Click on me to add a feature box here.';	
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