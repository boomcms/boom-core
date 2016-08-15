<?php

namespace BoomCMS\Page\Finder;

use Illuminate\Database\Eloquent\Builder;

/**
 * Finds the pages which are related pages of the given page.
 *
 * The relationship is outgoing
 * i.e. the relationship is listed in the given page's page relationships
 */
class RelationsOut extends AbstractPageFilter
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function execute(Builder $query)
    {
        return $query
            ->join('pages_relations', 'pages.id', '=', 'pages_relations.related_page_id')
            ->where('pages_relations.page_id', '=', $this->page->getId());
    }
}
