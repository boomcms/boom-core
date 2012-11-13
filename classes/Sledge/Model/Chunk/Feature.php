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
class Sledge_Model_Chunk_Feature extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_columns = array(
		'id'				=>	'',
		'target_page_id'	=>	'',
		'slotname'			=>	'',
	);

	protected $_belongs_to = array(
		'target' => array('model' => 'Page', 'foreign_key' => 'target_page_id'),
	);
}