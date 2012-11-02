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
	protected $_primary_key = 'chunk_id';
	protected $_table_columns = array(
		'chunk_id'			=> '',
		'target_page_id'	=> '',
	);
	
	protected $_belongs_to = array(
		'target' => array('model' => 'Page', 'foreign_key' => 'target_page_id'),
	);

	public function preview($template)
	{	
		$v = View::factory("site/slots/feature/$template");
		$v->target = $this->target;
	
		return $v;	
	}
}