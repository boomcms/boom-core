<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	Sledge
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Sledge_Chunk_Linkset extends Chunk
{
	protected $_default_template = 'quicklinks';

	protected $_type = 'linkset';

	protected function _show()
	{
		return View::factory("site/slots/linkset/$this->_template", array(
			'title'		=>	$this->_chunk->title,
			'links'	=>	$this->_chunk->links->find_all(),
		));
	}

	public function _show_default()
	{
		return View::factory("site/slots/default/linkset/$this->_template");
	}

	public function has_content()
	{
		return $this->_chunk->links->count_all() > 0;
	}
}