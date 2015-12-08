<?php

namespace BoomCMS\Foundation\Events;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\PageVersion;
use BoomCMS\Contracts\Models\Person;

class PageVersionEvent extends PageEvent
{
    /**
     * @var Person
     */
    protected $person;

    /**
     * @var Version
     */
    protected $version;

    public function __construct(Page $page, Person $person, PageVersion $version)
    {
        parent::__construct($page);

        $this->person = $person;
        $this->version = $version;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
