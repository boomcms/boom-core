<?php

/**
* Chunk model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk';
//	protected $_table_columns = array( 'id', 'slotname', 'type', 'active_vid' );
	protected $_belongs_to = array( 'chunk_text' => array( 'foreign_key' => 'id' ) );	
	protected $_has_many = array( 'pages' => array( 'model' => 'page', 'through' => 'chunk_page' ));
	
}

?>