<?php

namespace BoomCMS\Events;

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Template\Template;

class PageTemplateWasChanged extends AbstractPageEvent
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
