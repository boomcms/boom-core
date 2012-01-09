<?php

/**
* Slideshow chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Slideshow extends ORM implements iSLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_slideshow';
	protected $_has_one = array( 
		'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ),
	);
	protected $_has_many = array(
		'slides' => array( 'model' => 'slideshowimage', 'foreign_key' => 'chunk_id' ),
	);
	protected $_load_with = array( 'slides' );
	
	
	public function show()
	{
		$v = View::factory( 'site/slots/slideshow' );
		$v->chunk = $this;
		
		if ($this->loaded())
		{
			$v->title = $this->title;
			$v->slides = $this->slides->find_all();
		}
		else
		{		
			$slide = ORM::factory( 'slideshowimage' );
			$slide->url = '#';
			
			// Set some default values.
			$v->slides = array( $slide );
			$v->title = 'Default Slideshow';
		}	
			
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