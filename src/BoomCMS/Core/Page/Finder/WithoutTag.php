<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Tag\Tag;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WithoutTag extends Filter
{
    /**
     * @var Tag
     */
    protected $tag;

    /**
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function build(Builder $query)
    {
        return $query
            ->leftJoin('pages_tags as pt_without', function ($q) {
                $q
                    ->on('pages.id', '=', 'pt_without.page_id')
                    ->on('pt_without.tag_id', '=', DB::raw($this->tag->getId()));
            })
            ->whereNull('pt_without.page_id');
    }

    public function shouldBeApplied()
    {
        return $this->tag->loaded();
    }
}
