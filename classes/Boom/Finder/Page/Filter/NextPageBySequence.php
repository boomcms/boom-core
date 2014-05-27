<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class NextPageBySequence extends Finder\Filter
{
	/**
	 *
	 * @var \Boom\Page
	 */
	protected $currentPage;

	public function __construct(\Boom\Page $currentPage)
	{
		$this->currentPage = $currentPage;
	}

	public function execute(\ORM $query)
	{
		return $query
			->where('sequence', '>', $this->currentPage->getManualOrderPosition())
			->order_by('sequence', 'asc');
	}
}