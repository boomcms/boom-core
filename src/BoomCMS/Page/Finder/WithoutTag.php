<?php

namespace BoomCMS\Page\Finder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WithoutTag extends Tag
{
    public function build(Builder $query)
    {
        foreach ($this->tags as $i => $tag) {
            $alias = "pt_without-$i";

            $query
                ->leftJoin("pages_tags as $alias", function (Builder $q) use ($tag, $alias) {
                    $q
                        ->on('pages.id', '=', "$alias.page_id")
                        ->on("$alias.tag_id", '=', DB::raw($tag->getId()));
                })
                ->whereNull("$alias.page_id");
        }

        return $query;
    }
}
