<?php defined('SYSPATH') OR die('No direct script access.');

/**
* @package	BoomCMS
* @category	Chunks
*
*/
class Boom_Chunk_Asset extends Chunk
{
	protected $_asset;
	protected $_default_template = 'image';
	protected $_type = 'asset';

	public function __construct(\Boom\Page $page, $chunk, $editable = true)
	{
		parent::__construct($page, $chunk, $editable);

		$this->_asset = $this->_chunk->target;
	}

	protected function _show()
	{
		$v = View::factory($this->_view_directory."asset/$this->_template");

		// If the URL is just a number then assume it's the page ID for an internal link.
		if (preg_match('/^\d+$/D', $this->_chunk->url))
		{
			$target =  \Boom\Finder\Page::byId($this->_chunk->url);
			$v->title = $target->version()->title;
			$v->url = $target->url();
		}
		else
		{
			$v->title = $this->_chunk->title;
			$v->url = $this->_chunk->url;
		}

		$v->asset = $this->_chunk->target;
		$v->caption = $this->_chunk->caption;

		return $v;
	}

	protected function _show_default()
	{
		return View::factory($this->_view_directory."default/asset/$this->_template");
	}

	public function attributes()
	{
		return array(
			$this->_attribute_prefix.'target' => $this->target(),
		);
	}

	public function asset()
	{
		return $this->_asset;
	}

	public function has_content()
	{
		return $this->_chunk->loaded() && $this->_asset->loaded();
	}

	public function target()
	{
		return $this->_asset->id;
	}
}