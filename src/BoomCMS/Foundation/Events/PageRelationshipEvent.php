<?php

namespace BoomCMS\Foundation\Events;

use BoomCMS\Contracts\Models\Page;

abstract class PageRelationshipEvent extends PageEvent
{
    /**
     * @var Page
     */
    protected $relatedPage;

    /**
     * 
     * @param Page $page
     * @param Page $relatedPage
     */
    public function __construct(Page $page, $relatedPage)
    {
        $this->page = $page;
        $this->relatedPage = $relatedPage;
    }

    /**
     * @return Page
     */
    public function getRelatedPage()
    {
        return $this->relatedPage;
    }
}
