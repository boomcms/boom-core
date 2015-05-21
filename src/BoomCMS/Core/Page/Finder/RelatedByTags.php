<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Page\Page as Page;
use BoomCMS\Core\Finder\Filter;

use Illuminate\Database\Eloquent\Builder;

class RelatedByTags extends Filter
{
    /**
	 *
	 * @var array
	 */
    protected $_tagIds;

    /**
	 *
	 * @var Page
	 */
    protected $_page;

    public function __construct(Page $page)
    {
        $this->_page = $page;
        $this->_tagIds = $this->_getTagIds();
    }

    public function execute(Builder $query)
    {
        return $query
            ->select([\DB::raw('count(pages_tags.tag_id)'), 'tag_count'])
            ->join('pages_tags', 'page.id', '=', 'pages_tags.page_id')
            ->where('tag_id', 'in', $this->_tagIds)
            ->where('page.id', '!=', $this->_page->getId())
            ->orderBy('tag_count', 'desc')
            ->orderBy(\DB::raw('rand()'))
            ->groupBy('page.id');
    }

    /**
	 * TODO: This should probably be in a \Boom\Page\Tags class
	 */
    protected function _getTagIds()
    {
        $results = \DB::select('tag_id')
            ->from('pages_tags')
            ->where('page_id', '=', $this->_page->getId())
            ->execute();

        return \Arr::pluck($results, 'tag_id');
    }

    public function shouldBeApplied()
    {
        return \count($this->_tagIds) > 0;
    }
}
