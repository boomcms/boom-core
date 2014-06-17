<?php

namespace Boom\Page\Finder\Filter;

class ParentId extends \Boom\Finder\Filter
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