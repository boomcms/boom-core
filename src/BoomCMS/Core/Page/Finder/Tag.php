<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Filter
{
    /**
	 *
	 * @var \Boom\Tag\Tag
	 */
    protected $_tag;

    public function __construct(\Boom\Tag\Tag $tag)
    {
        $this->_tag = $tag;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('pages_tags', 'page.id', '=', 'pages_tags.page_id')
            ->where('pages_tags.tag_id', '=', $this->_tag->getId());
    }

    public function shouldBeApplied()
    {
        return $this->_tag->loaded();
    }
}
