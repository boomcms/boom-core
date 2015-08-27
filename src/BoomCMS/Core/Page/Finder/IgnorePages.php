<?php

namespace BoomCMS\Core\Page\Finder;

use Illuminate\Database\Eloquent\Builder;

/**
 * Set Page IDs to remove from results.
 */
class IgnorePages extends PageId
{
    public function build(Builder $query)
    {
        if (is_array($this->pageId)) {
            return $query->where('pages.id', 'not in', $this->pageId);
        } else {
            return $query->where('pages.id', '!=', $this->pageId);
        }
    }
}
