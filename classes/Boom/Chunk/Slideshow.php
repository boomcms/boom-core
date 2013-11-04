<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * @package	BoomCMS
 * @category	Chunks
 *
 */
class Boom_Chunk_Slideshow extends Chunk
{
	protected $_default_template = 'circles';

	protected $_type = 'slideshow';

	protected function _show()
	{
		return View::factory($this->_view_directory."slideshow/$this->_template", array(
			'chunk'	=>	$this->_chunk,
			'title'		=>	$this->_chunk->title,
			'slides'	=>	$this->_chunk->slides(),
			'editor'	=>	Editor::instance(),
		));
	}

	public function _show_default()
	{
		return View::factory($this->_view_directory."default/slideshow/$this->_template");
	}

	public function has_content()
	{
		return $this->_chunk->loaded() AND count($this->_chunk->slides()) > 0;
	}

	public function slides()
	{
		return $this->_chunk->slides();
	}

	public function thumbnail()
	{
		return $this->_chunk->thumbnail();
	}
}