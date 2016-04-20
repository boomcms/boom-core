<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class AppliedToPageDescendants extends Filter
{
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function build(Builder $query)
    {
        return $query
            ->join('pages_tags', 'tags.id', '=', 'pages_tags.tag_id')
            ->join('pages', 'pages_tags.page_id', '=', 'pages.id')
            ->where(function (Builder $nested) {
                $nested
                    ->where('pages.id', '=', $this->page->getId())
                    ->orWhere('pages.parent_id', '=', $this->page->getId());
            })
            ->whereNull('pages.deleted_at')
            ->groupBy('tags.id')
            ->orderBy('tags.name', 'asc');
    }
}
