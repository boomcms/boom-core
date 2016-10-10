<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Contracts\Models\Tag as TagInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AllTags extends Tag
{
    public function build(Builder $query)
    {
        $tagIds = array_map(function(TagInterface $tag) {
            return $tag->getId();
        }, $this->tags);

        return $query
            ->join('pages_tags', 'pages.id', '=', 'pages_tags.page_id')
            ->whereIn('pages_tags.tag_id', $tagIds)
            ->groupBy('pages_tags.tag_id')
            ->having(DB::raw('count(distinct pages_tags.tag_id)'), '=', count($tagIds));
    }
}
