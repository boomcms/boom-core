<?php

namespace BoomCMS\Foundation\Events;

use BoomCMS\Contracts\Models\Page;

abstract class PageEvent
{
    /**
     * @var Page
     */
    protected $page;

    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
