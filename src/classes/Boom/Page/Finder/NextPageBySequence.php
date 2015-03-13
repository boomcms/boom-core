<?php

namespace Boom\Page\Finder;

use Boom\Page\Page;
use Boom\Model\Page as Model;

class NextPageBySequence extends \Boom\Finder\Filter
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

    public function execute(Model $query)
    {
        return $query
            ->where('sequence', '>', $this->currentPage->getManualOrderPosition())
            ->orderBy('sequence', 'asc');
    }
}
