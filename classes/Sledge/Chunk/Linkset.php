<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
class Sledge_Chunk_Linkset extends Chunk
{
	protected $_default_template = 'quicklinks';

	protected $_type = 'linkset';

	protected function _show()
	{
		$v = View::factory("site/slots/linkset/$this->_template");

		$v->title = $this->_chunk->title;
		$v->links = $this->_chunk->links();

		return $v;
	}

	public function _show_default()
	{
		return View::factory("site/slots/default/linkset/$this->_template");
	}

	public function has_content()
	{
		$links = $this->_chunk->links();

		return ! empty($links);
	}
}