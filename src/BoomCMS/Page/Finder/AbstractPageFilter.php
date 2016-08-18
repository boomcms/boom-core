<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Page as PageFacade;

abstract class AbstractPageFilter extends Filter
{
    /**
     * @var Page
     */
    protected $page;

    /**
     * @param Page|int $page
     */
    public function __construct($page)
    {
        $this->page = ($page instanceof Page) ? $page : PageFacade::find($page);
    }

    public function shouldBeApplied()
    {
        return $this->page !== null;
    }
}
