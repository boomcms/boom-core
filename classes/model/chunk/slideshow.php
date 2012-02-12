<?php

/**
* Slideshow chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Slideshow extends ORM implements Interface_SLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_slideshow';
	protected $_primary_key = 'chunk_id';
	protected $_has_many = array(
		'slides' => array( 'model' => 'slideshowimage', 'foreign_key' => 'chunk_id' ),
	);
	protected $_load_with = array( 'slides' );
	
	
	public function show()
	{	
		if ($this->loaded())
		{
			$v = View::factory( 'site/slots/slideshow' );
			$v->chunk = $this;
			
			$v->title = $this->title;
			$v->slides = $this->slides->find_all();	
			
			return $v;
		}	
	}
	
	public function show_default()
	{
		return "Click on me to add a slideshow here";		
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
