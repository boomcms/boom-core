<?php

namespace Boom\Page\Finder\Filter;

class ParentPage extends \Boom\Finder\Filter
{
	protected $parent;

	public function __construct($parent)
	{
		$this->parent = $parent;
	}

	public function execute(\ORM $query)
	{
		$order = $this->parent->getChildOrderingPolicy();

		return $query
			->join('page_mptt', 'inner')
			->on('page.id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $this->parent->getId())
			->order_by($order->getColumn(), $order->getDirection());
	}
}