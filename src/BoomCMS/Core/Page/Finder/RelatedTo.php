<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Database\Eloquent\Builder;

class RelatedTo extends Filter
{
    protected $page;

    public function __construct($page)
    {
        $this->page = ($page instanceof Page) ? $page : PageFacade::find($page);
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('pages_relations', 'pages.id', '=', 'pages_relations.related_page_id')
            ->where('pages_relations.page_id', '=', $this->page->getId());
    }

    public function shouldBeApplied()
    {
        return $this->page !== null;
    }
}
