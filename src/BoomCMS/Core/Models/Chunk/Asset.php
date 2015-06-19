<?php

namespace BoomCMS\Core\Models\Chunk;

use BoomCMS\Link\Link;

class Asset extends BaseChunk
{
    protected $table = 'chunk_assets';

	public function setCaptionAttribute($value)
	{
		$this->attributes['caption'] = strip_tags($value);
	}

	public function setTitleAttribute($value)
	{
		$this->attributes['title'] = strip_tags($value);
	}

	public function setUrlAttribute($value)
	{
		$link = Link::factory($value);

		$this->attributes['url'] = $link->isInternal() ?
			$link->getPage()->getId() :
			$link->url();
	}
}
