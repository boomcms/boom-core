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
class Sledge_Model_Chunk_Linkset_Link extends ORM
{
	protected $_belongs_to = array(
		'target'	=> array('model' => 'page', 'foreign_key' => 'target_page_id')
	);

	protected $_table_columns = array(
		'id'				=>	'',
		'target_page_id'	=>	'',
		'chunk_linkset_id'	=>	'',
		'url'				=>	'',
		'title'				=>	'',
	);

	protected $_table_name = 'chunk_linkset_links';

	/**
	* Is the link internal?
	*
	* @return boolean
	*/
	public function is_internal()
	{
		return (int) $this->target_page_id != 0;
	}

	/**
	* Is the link external?
	* Alias for is_internal()
	*/
	public function is_external()
	{
		return ! $this->is_internal();
	}
}