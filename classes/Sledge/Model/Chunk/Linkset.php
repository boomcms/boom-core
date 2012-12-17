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
class Sledge_Model_Chunk_Linkset extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_many = array(
		'links' => array('model' => 'Chunk_Linkset_Link', 'foreign_key' => 'chunk_linkset_id'),
	);
	protected $_load_with = array('links');
	protected $_table_columns = array(
		'id'		=>	'',
		'title'		=>	'',
		'slotname'	=>	'',
	);

	protected $_table_name = 'chunk_linksets';
}