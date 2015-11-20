<?php

namespace BoomCMS\Foundation\Events;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Tag;

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
