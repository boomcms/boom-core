<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Find pages by a single or array of page IDs
 */
class PageId extends Filter
{
    /**
     * @var array|int 
     */
    protected $pageId;

    /**
     * 
     * @param array|int  $pageId
     */
    public function __construct($pageId)
    {
        $this->pageId = $pageId;
    }

    public function build(Builder $query)
    {
        if (is_array($this->pageId)) {
            return $query->where('pages.id', 'in', $this->pageId);
        } else {
            return $query->where('pages.id', '=', $this->pageId);
        }
    }

    public function shouldBeApplied()
    {
        return (is_int($this->pageId) || 
            ctype_digit($this->pageId) || 
            is_array($this->pageId)
        ) && !empty($this->pageId);
    }
}
