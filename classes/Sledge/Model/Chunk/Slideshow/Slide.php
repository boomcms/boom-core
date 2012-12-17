<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Holds a list of images which belong to a slideshow slot.
 *
 *
 * @package	Sledge
 * @category	Chunks
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 *
 */
class Sledge_Model_Chunk_Slideshow_Slide extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_has_one = array(
		'asset'	=>	array('model' => 'asset', 'foreign_key' => 'id')
	);
	protected $_table_columns = array(
		'id'		=>	'',
		'asset_id'	=>	'',
		'url'		=>	'',
		'chunk_id'	=>	'',
		'caption'	=>	'',
		'title'		=>	'',
	);

	protected $_table_name = 'chunk_slideshow_slides';
}