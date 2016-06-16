<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Database\Eloquent\Builder;

/**
 * Finds the pages which reference this page in their page relationships.
 *
 * The relationship is incoming
 * i.e. the given page is listed in other pages related pages.
 */
class RelationsIn extends Filter
{
    protected $page;

    /**
     * @param Page $page
     */
    public function __construct($page)
    {
        $this->page = ($page instanceof Page) ? $page : PageFacade::find($page);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function execute(Builder $query)
    {
        return $query
            ->join('pages_relations', 'pages.id', '=', 'pages_relations.page_id')
            ->where('pages_relations.related_page_id', '=', $this->page->getId());
    }

    public function shouldBeApplied()
    {
        return $this->page !== null;
    }
}
