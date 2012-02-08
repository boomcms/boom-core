<?php
/**
* Join table for chunks and page versions.
*
* Table name: chunk_page
*
*************************** Table Columns ************************
****	Name			****	Data Type	****	Description					
****	page_vid		****	integer		****	ID of the page version the chunk belongs to.
****	chunk_id		****	integer		****	ID of the chunk being slotted.
******************************************************************
*
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
	protected $_belongs_to = array( 'version_page' => array( ), 'chunk' => array( 'foreign_key' => 'id' ) );
	protected $_has_one = array( 'chunk' => array( 'model' => 'chunk', 'foreign_key' => 'id' ));
	protected $_load_with = array( 'chunk' );

}

?>