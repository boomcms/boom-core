<?php

namespace BoomCMS\Page\Finder;

use Illuminate\Database\Eloquent\Builder;

/**
 * Finds the pages which reference this page in their page relationships.
 *
 * The relationship is incoming
 * i.e. the given page is listed in other pages related pages.
 */
class RelationsIn extends AbstractPageFilter
{
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
}
