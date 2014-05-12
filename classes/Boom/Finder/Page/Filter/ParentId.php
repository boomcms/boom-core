<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class ParentId extends Finder\Filter
{
	protected $_parentId;

	public function __construct($parentId)
	{
		$this->_parentId = $parentId;
	}

	public function execute(\ORM $query)
	{
		return $query
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $this->_parentId);
	}
}