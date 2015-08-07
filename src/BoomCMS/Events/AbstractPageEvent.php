<?php

namespace BoomCMS\Events;

use BoomCMS\Core\Page\Page;

abstract class AbstractPageEvent
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