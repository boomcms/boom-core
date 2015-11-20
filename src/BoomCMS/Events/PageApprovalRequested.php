<?php

namespace BoomCMS\Events;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Events\PageEvent;

class PageApprovalRequested extends PageEvent
{
    protected $requestedBy;

    public function __construct(Page $page, Person $person)
    {
        parent::__construct($page);

        $this->requestedBy = $person;
    }

    public function getRequestedBy()
    {
        return $this->requestedBy;
    }
}
