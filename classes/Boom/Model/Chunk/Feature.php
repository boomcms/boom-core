<?php defined('SYSPATH') OR die('No direct script access.');
/**
*
* @package	BoomCMS
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
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
	);

	protected $_table_name = 'chunk_features';
}