<?php

namespace BoomCMS\Events;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Template;
use BoomCMS\Foundation\Events\PageEvent;

class PageTemplateWasChanged extends PageEvent
{
    /**
     * @var Template
     */
    protected $newTemplate;

    public function __construct(Page $page, Template $newTemplate)
    {
        parent::__construct($page);

        $this->newTemplate = $newTemplate;
    }

    public function getNewTemplate()
    {
        return $this->newTemplate;
    }
}
