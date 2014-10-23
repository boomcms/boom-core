<?php

namespace Boom\Tag\Finder\Filter;

class Asset extends \Boom\Finder\Filter
{
	protected $asset;

	public function __construct(\Boom\Asset $asset)
	{
		$this->asset = $asset;
	}

	public function execute(\ORM $query)
	{
		return $query
			->join('assets_tags', 'inner')
			->on('tag.id', '=', 'assets_tags.tag_id')
			->where('assets_tags.asset_id', '=', $this->asset->getId());
	}
}