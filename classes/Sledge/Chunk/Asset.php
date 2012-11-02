<?php defined('SYSPATH') OR die('No direct script access.');
/**
* @package Sledge
* @category Chunks
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*
*/
class Sledge_Chunk_Asset extends Chunk
{
	protected $_asset;

	protected $_default_template = 'image';

	protected $_type = 'asset';

	protected function _show()
	{
		$v = View::factory("site/slots/asset/$this->_template");
		
		// If the URL is just a number then assume it's the page ID for an internal link.
		if (preg_match('/^\d+$/D', $this->_chunk->url))
		{
			$target = ORM::factory('Page', $this->_chunk->url);
			$v->title = $target->title;
			$v->url = $target->url();
		}
		else
		{
			$v->title = $this->_chunk->title;
			$v->url = $this->_chunk->url;
		}
		
		$v->asset = $this->_chunk->asset;
		$v->caption = $this->_chunk->caption;

		return $v;
	}
	
	protected function _show_default()
	{
		return View::factory("site/slots/default/asset/$this->_template");
	}

	public function asset()
	{
		if ($this->_asset === NULL)
		{
			$this->_asset = ORM::factory('Asset', $this->_chunk->asset_id);
		}

		return $this->_asset;
	}

	public function target()
	{
		return $this->asset()->pk();
	}
}