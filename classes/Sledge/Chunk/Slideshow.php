<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
class Sledge_Chunk_Slideshow extends Chunk
{
	protected $_default_template = 'circles';

	protected $_type = 'slideshow';

	protected function _show()
	{
		$v = View::factory("site/slots/slideshow/$this->_template");
		$v->chunk = $this->_chunk;
		$v->title = $this->_chunk->title;
		$v->slides = $this->_chunk->slides();

		return $v;
	}

	public function _show_default()
	{
		return View::factory("site/slots/default/slideshow/$this->_template");
	}

	public function has_content()
	{
		$slides = $this->_chunk->slides();

		return ! empty($slides);
	}

	public function target()
	{
		return implode("-", $this->_chunk->get_asset_ids());
	}
}