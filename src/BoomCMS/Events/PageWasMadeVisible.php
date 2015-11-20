<?php

namespace BoomCMS\Events;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Events\PageEvent;

class PageWasMadeVisible extends PageEvent
{
    /**
     * @var Person
     */
    protected $person;

    public function __construct(Page $page, Person $person)
    {
        parent::__construct($page);

        $this->person = $person;
    }

    public function getPerson()
    {
        return $this->person;
    }
}
