<?php defined('SYSPATH') OR die('No direct script access.');
/**
*
* @package	Sledge
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Page_Chunk extends ORM
{
	/**
	* Properties to define table relationships.
	*/
	protected $_belongs_to = array('Page_Versions' => array(), 'chunk' => array('foreign_key' => 'id'));
	protected $_has_one = array('chunk' => array('foreign_key' => 'id'));
	protected $_load_with = array('chunk');
	protected $_table_columns = array(
		'page_versions.id'	=>	'',
		'chunk_id'	=>	'',
	);

}
