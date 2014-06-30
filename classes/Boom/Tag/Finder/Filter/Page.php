<?php

namespace Boom\Tag\Finder\Filter;

class Page extends \Boom\Finder\Filter
{
	protected $page;

	public function __construct(\Boom\Page $page)
	{
		$this->page = $page;
	}

	public function execute(\ORM $query)
	{
		return $query
			->join('pages_tags', 'inner')
			->on('tag.id', '=', 'pages_tags.tag_id')
			->where('pages_tags.page_id', '=', $this->page->getId());
	}
}