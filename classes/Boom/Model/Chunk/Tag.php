<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package	BoomCMS
 * @category	Chunks
 * @category	Models
 *
 */
class Boom_Model_Chunk_Tag extends ORM
{
	protected $_belongs_to = array(
		'target' => array('model' => 'Tag', 'foreign_key' => 'tag_id'),
	);

	protected $_table_columns = array(
		'id' => '',
		'slotname'	=> '',
		'tag_id' => '',
		'page_vid' => '',
	);

	protected $_table_name = 'chunk_tags';
}