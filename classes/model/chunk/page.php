<?php

/**
* Chunk_page model
* @package Models
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2011, Hoop Associates
*
*/
class Model_Chunk_Page extends ORM
{
	/**
	* Properties to define table relationships.
	*/
	protected $_table_name = 'chunk_page';
	protected $_belongs_to = array( 'page' => array( ) );
	protected $_has_one = array( 'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'id' ));
	protected $_load_with = array( 'chunk' );

}

?>