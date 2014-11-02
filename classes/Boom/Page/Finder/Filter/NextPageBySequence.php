<?php

namespace Boom\Page\Finder\Filter;

class NextPageBySequence extends \Boom\Finder\Filter
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
