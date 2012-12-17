<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Model for the asset chunk table.
*
* @package	Sledge
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Chunk_Asset extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_belongs_to = array(
		'asset' => array('model' => 'Asset', 'foreign_key' => 'asset_id'),
	);

	protected $_table_columns = array(
		'id'			=>	'',
		'asset_id'		=>	'',
		'title'			=>	'',
		'caption'		=>	'',
		'url'			=>	'',
		'slotname'		=>	'',
	);

	protected $_table_name = 'chunk_assets';
}