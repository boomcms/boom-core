<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RelatedByTags extends Filter
{
    /**
     * @var array
     */
    protected $tagIds;

    /**
     * @var Page
     */
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->tagIds = $this->getTagIds();
    }

    public function build(Builder $query)
    {
        return $query
            ->addSelect(DB::raw('count(pages_tags.tag_id) as tag_count'))
            ->join('pages_tags', 'pages.id', '=', 'pages_tags.page_id')
            ->whereIn('tag_id', $this->tagIds)
            ->where('pages.id', '!=', $this->page->getId())
            ->orderBy('tag_count', 'desc')
            ->orderBy(DB::raw('rand()'))
            ->groupBy('pages.id');
    }

    /**
     * TODO: This should probably be in a \Boom\Page\Tags class.
     */
    protected function getTagIds()
    {
        return DB::table('pages_tags')
            ->select('tag_id')
            ->where('page_id', '=', $this->page->getId())
            ->lists('tag_id');
    }

    public function shouldBeApplied()
    {
        return \count($this->tagIds) > 0;
    }
}
