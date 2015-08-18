<?php

namespace BoomCMS\Events;

use BoomCMS\Foundation\Events\PageEvent;
use BoomCMS\Core\Page\Page;

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
