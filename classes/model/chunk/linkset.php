<?php

/**
* Linkset chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Linkset extends ORM implements iSLot
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_linkset';
	protected $_has_one = array( 
		'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'active_vid' ),
	);
	protected $_has_many = array(
		'links' => array( 'model' => 'linksetlink' ),
	);
	protected $_load_with = array( 'links' );
	
	
	public function show()
	{
		$v = View::factory( 'site/slots/linkset' );
		
		if ($this->loaded())
		{
			$v->title = $this->title;
			$v->links = $this->links->find_all();
		}
		else
		{		
			$link = ORM::factory( 'linksetlink' );
			$link->title = 'Add Link';
			$link->url = '#';
			
			// Set some default values.
			$v->links = array( $link );
			$v->title = 'Default Linkset';
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