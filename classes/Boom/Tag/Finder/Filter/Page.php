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
		
	}
}