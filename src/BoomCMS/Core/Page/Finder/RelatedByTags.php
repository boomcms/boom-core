<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Page\Page as Page;
use BoomCMS\Core\Finder\Filter;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class RelatedByTags extends Filter
{
    /**
	 *
	 * @var array
	 */
    protected $tagIds;

    /**
	 *
	 * @var Page
	 */
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
        $this->tagIds = $this->getTagIds();
    }

    public function execute(Builder $query)
    {
        return $query
            ->select([DB::raw('count(pages_tags.tag_id)'), 'tag_count'])
            ->join('pages_tags', 'page.id', '=', 'pages_tags.page_id')
            ->whereIn('tag_id', $this->tagIds)
            ->where('page.id', '!=', $this->page->getId())
            ->orderBy('tag_count', 'desc')
            ->orderBy(DB::raw('rand()'))
            ->groupBy('page.id');
    }

    /**
	 * TODO: This should probably be in a \Boom\Page\Tags class
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
