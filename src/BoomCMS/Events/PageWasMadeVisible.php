<?php

namespace BoomCMS\Events;

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Person\Person;

class PageWasMadeVisible extends AbstractPageEvent
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