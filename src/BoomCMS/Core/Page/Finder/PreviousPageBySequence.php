<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Finder\Filter;

use Illuminate\Database\Eloquent\Builder;

class PreviousPageBySequence extends Filter
{
    /**
	 *
	 * @var \Boom\Page
	 */
    protected $currentPage;

    public function __construct(Page $currentPage)
    {
        $this->currentPage = $currentPage;
    }

    public function execute(Builder $query)
    {
        return $query
            ->where('sequence', '<', $this->currentPage->getManualOrderPosition())
            ->orderBy('sequence', 'desc');
    }
}
