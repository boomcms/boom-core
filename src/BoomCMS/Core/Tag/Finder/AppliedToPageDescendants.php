<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Core\Finder\Filter;
use BoomCMS\Core\Page\Page;
use Illuminate\Database\Eloquent\Builder;

class AppliedToPageDescendants extends Filter
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function execute(Builder $query)
    {
        $page = $this->page;

        return $query
            ->join('pages_tags', 'tags.id', '=', 'pages_tags.tag_id')
            ->join('pages', 'pages_tags.page_id', '=', 'pages.id')
            ->where(function ($query) use ($page) {
                $query
                    ->where('pages.id', '=', $page->getId())
                    ->orWhere('pages.parent_id', '=', $page->getId());
            })
            ->distinct(true)
            ->orderBy('tags.name', 'asc');
    }
}
