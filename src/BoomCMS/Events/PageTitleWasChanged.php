<?php

namespace BoomCMS\Events;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Events\PageEvent;

class PageTitleWasChanged extends PageEvent
{
    /**
     * @var string
     */
    protected $newTitle;

    /**
     * @var string
     */
    protected $oldTitle;

    public function __construct(Page $page, $oldTitle, $newTitle)
    {
        parent::__construct($page);

        $this->oldTitle = $oldTitle;
        $this->newTitle = $newTitle;
    }

    /**
     * @return string
     */
    public function getNewTitle()
    {
        return $this->newTitle;
    }

    /**
     * @return string
     */
    public function getOldTitle()
    {
        return $this->oldTitle;
    }
}
