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
        return $query
            ->join('pages_tags', 'inner')
            ->on('tag.id', '=', 'pages_tags.tag_id')
            ->join('pages', 'inner')
            ->on('pages_tags.page_id', '=', 'pages.id')
            ->join('page_mptt', 'inner')
            ->on('pages.id', '=', 'page_mptt.id')
            ->where('page_mptt.lft', '>=', $this->page->getMptt()->lft)
            ->where('page_mptt.rgt', '<=', $this->page->getMptt()->rgt)
            ->where('page_mptt.scope', '=', $this->page->getMptt()->scope)
            ->distinct(true)
            ->orderBy('tag.name', 'asc');
    }
}
