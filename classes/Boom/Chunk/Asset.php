<?php

namespace Boom\Chunk;

use \Boom\Page as Page;
use Page\Finder as PageFinder;
use \Boom\Link\Link as Link;
use \View as View;

class Asset extends \Boom\Chunk
{
	protected $_asset;
	protected $_default_template = 'image';
	protected $_type = 'asset';

	public function __construct(Page\Page $page, $chunk, $editable = true)
	{
		parent::__construct($page, $chunk, $editable);

		$this->_asset = \Boom\Asset::factory($this->_chunk->target);
	}

	protected function _show()
	{
		$v = new View($this->viewDirectory."asset/$this->_template", array(
			'asset' => $this->asset(),
			'caption' => $this->_chunk->caption
		));

		$link = Link::factory($this->_chunk->url);
		if ($link->isInternal()) {
			$target = PageFinder::byId($this->_chunk->url);
			$v->set(array(
				'title' => $target->getTitle(),
				'url' => $target->url()
			));
		} else {
			$v->set(array(
				'title' => $this->_chunk->title,
				'url' => $this->_chunk->url,
			));
		}

		return $v;
	}

	protected function _show_default()
	{
		return new View($this->viewDirectory."default/asset/$this->_template");
	}

	public function attributes()
	{
		return array(
			$this->attributePrefix.'target' => $this->target(),
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
		return $this->_asset->getId();
	}
}