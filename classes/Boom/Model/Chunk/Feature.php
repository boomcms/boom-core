<?php defined('SYSPATH') OR die('No direct script access.');
/**
 *
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 *
 */
class Boom_Model_Chunk_Feature extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_columns = array(
		'id'				=>	'',
		'target_page_id'	=>	'',
		'slotname'			=>	'',
		'page_vid' => '',
	);

	protected $_belongs_to = array('target' => array('model' => 'Page', 'foreign_key' => 'target_page_id'));

	protected $_table_name = 'chunk_features';
}