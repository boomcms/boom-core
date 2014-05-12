<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class ParentPage extends Finder\Filter
{
	protected $_parent;

	public function __construct($parent)
	{
		$this->_parent = $parent;
	}

	public function execute(\ORM $query)
	{
		$order = $this->_parent->getChildOrderingPolcy();

		return $query
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $this->_parent->id)
			->order_by($order->getColumn(), $order->getDirection());
	}
}