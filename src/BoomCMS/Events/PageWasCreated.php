<?php

namespace BoomCMS\Events;

use BoomCMS\Core\Page\Page;
use BoomCMS\Foundation\Events\PageEvent;

class PageWasCreated extends PageEvent
{
    /**
     * @var Page
     */
    protected $parent;

    public function __construct(Page $page, Page $parent = null)
    {
        $this->page = $page;
        $this->parent = $parent;
    }

    /**
     * @return Page
     */
    public function getParent()
    {
        return $this->parent;
    }
}
