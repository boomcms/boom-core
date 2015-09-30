<?php

namespace BoomCMS\Foundation\Events;

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Tag\Tag;

abstract class PageTagEvent extends PageEvent
{
    /**
     * @var Tag
     */
    protected $tag;

    public function __construct(Page $page, Tag $tag)
    {
        $this->page = $page;
        $this->tag = $tag;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }
}
