<?php defined('SYSPATH') OR die('No direct script access.');
/**
* Tag chunk model
 *
* @package	Sledge
* @category	Chunks
* @category	Models
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Model_Chunk_Tag extends ORM
{
	/**
	* Properties to create relationships with Kohana's ORM
	*/
	protected $_table_name = 'chunk_tag';
	protected $_primary_key = 'chunk_id';
		
	protected $_table_columns = array(
		'chunk_id'	=>	'',
		'tag_id'	=>	'',
	);

	public function preview($template)
	{
		return Request::factory('sledge/asset/library')->post( array('parent_tag' => $this->tag_id, 'tag' => $this->tag_id, 'url' => '#'))->execute();
	}
}