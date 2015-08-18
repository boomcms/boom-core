<?php

namespace BoomCMS\Foundation\Events;

use BoomCMS\Core\Person\Person;
use Illuminate\Http\Request;

class AuthEvent
{
    /**
     * @var Person 
     */
    protected $person;

    /**
     * @var Request 
     */
    protected $request;

    public function __construct(Person $person, Request $request)
    {
        $this->person = $person;
        $this->request = $request;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}