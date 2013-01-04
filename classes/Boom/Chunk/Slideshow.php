<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package	BoomCMS
* @category	Chunks
* @author	Rob Taylor
* @copyright	Hoop Associates
*
*/
class Boom_Chunk_Slideshow extends Chunk
{
	protected $_default_template = 'circles';

	protected $_type = 'slideshow';

	protected function _show()
	{
		return View::factory("site/slots/slideshow/$this->_template", array(
			'chunk'	=>	$this->_chunk,
			'title'		=>	$this->_chunk->title,
			'slides'	=>	$this->_chunk->slides(),
			'editor'	=>	Editor::instance(),
		));
	}

	public function _show_default()
	{
		return View::factory("site/slots/default/slideshow/$this->_template");
	}

	public function has_content()
	{
		return count($this->_chunk->slides()) > 0;
	}
}