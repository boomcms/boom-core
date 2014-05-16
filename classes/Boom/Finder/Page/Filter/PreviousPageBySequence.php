<?php

namespace Boom\Finder\Page\Filter;

use \Boom\Finder as Finder;

class PreviousPageBySequence extends Finder\Filter
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
			->where('sequence', '<', $this->currentPage->sequence)
			->order_by('sequence', 'desc');
	}
}