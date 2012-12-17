<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	Sledge
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Tag_Applied extends ORM
{

	/**
	* The value of the object_type column for relationships with assets.
	*/
	const OBJECT_TYPE_ASSET = 1;

	const OBJECT_TYPE_PAGE = 2;

	protected $_table_columns = array(
		'tag_id'		=>	'',
		'object_type'	=>	'',
		'object_id'		=>	'',
	);

	protected $_table_name = 'tags_applied';
}