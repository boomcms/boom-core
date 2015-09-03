<?php

namespace BoomCMS\Core\Tag\Finder;

use BoomCMS\Core\Tag\Tag;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class AppliedWith extends Filter
{
    protected $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('pages_tags as pt1', 'tags.id', '=', 'pt1.tag_id')
            ->join('pages_tags as pt2', 'pt1.page_id', '=', 'pt2.page_id')
            ->where('pt2.tag_id', '=', $this->tag->getId())
            ->distinct()
            ->groupBy('pt1.tag_id');
    }

    public function shouldBeApplied()
    {
        return $this->tag->loaded();
    }
}
