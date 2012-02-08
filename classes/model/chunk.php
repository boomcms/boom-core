<?php

/**
* Chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*/
class Model_Chunk extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk';
	protected $_belongs_to = array( 'chunk_text' => array( 'foreign_key' => 'id' ) );		
	
	public function __construct( $type = null, $id = null )
	{
		parent::__construct( $id );
		
		if ($type !== null)
		{
			$slot = array(
	        	'model' => "chunk_$type",
	        	'foreign_key' => "active_vid",
			);

	        $this->_belongs_to['slot'] = $slot;
			$this->with( 'slot' );
		}
	}
}

?>