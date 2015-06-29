<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Finder\Filter;
use BoomCMS\Core\Tag\Tag as TagObject;

use Illuminate\Database\Eloquent\Builder;

class Tag extends Filter
{
    /**
	 *
	 * @var TagObject
	 */
    protected $tag;

    public function __construct(TagObject $tag)
    {
        $this->tag = $tag;
    }

    public function execute(Builder $query)
    {
        return $query
            ->join('pages_tags', 'page.id', '=', 'pages_tags.page_id')
            ->where('pages_tags.tag_id', '=', $this->tag->getId());
    }

    public function shouldBeApplied()
    {
        return $this->tag->loaded();
    }
}
