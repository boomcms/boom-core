<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;

use Illuminate\Database\Eloquent\Builder;

class RelatedTo extends Filter
{
    protected $page;

    public function __construct($page)
    {
        $this->page = $page;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('pages_relations', 'pages.id', '=', 'pages_relations.related_page_id')
            ->where('pages_relations.page_id', '=', $this->page->id);
    }

    public function shouldBeApplied()
    {
        return $this->page->loaded();
    }
}
