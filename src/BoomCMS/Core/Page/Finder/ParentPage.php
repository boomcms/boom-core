<?php

namespace BoomCMS\Core\Page\Finder;

use Illuminate\Database\Eloquent\Builder;

class ParentPage extends AbstractPageFilter
{
    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function build(Builder $query)
    {
        if ($this->page === null) {
            return $query->whereNull('parent_id');
        }

        list($col, $direction) = $this->page->getChildOrderingPolicy();

        return $query
            ->where('parent_id', '=', $this->page->getId())
            ->orderBy($col, $direction);
    }

    /**
     * @return boolean
     */
    public function shouldBeApplied()
    {
        return true;
    }
}
