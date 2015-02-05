<?php

namespace Boom\Page\Finder\Filter;

class Tag extends \Boom\Finder\Filter
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

    public function execute(\ORM $query)
    {
        return $query
            ->join('pages_tags', 'inner')
            ->on('page.id', '=', 'pages_tags.page_id')
            ->where('pages_tags.tag_id', '=', $this->_tag->getId());
    }

    public function shouldBeApplied()
    {
        return $this->_tag->loaded();
    }
}
