<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Set Page IDs to remove from results.
 */
class IgnorePages extends Filter
{
    protected $pages;
    
    public function __construct($pages) {
        $this->pages = $pages;
    }
    public function build(Builder $query)
    {
        if (is_array($this->pages)) {
            return $query->whereNotIn('pages.id', $this->pages);
        } else {
            return $query->where('pages.id', '!=', $this->pages);
        }
    }
}
