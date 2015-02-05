<?php

namespace Boom\Tag\Finder\Filter;

use Boom\Page\Page;

class AppliedToPageDescendants extends \Boom\Finder\Filter
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function execute(\ORM $query)
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
            ->order_by('tag.name', 'asc');
    }
}
