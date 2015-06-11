<?php

namespace BoomCMS\Core\Auth\Auth;

use BoomCMS\Core\Person\Person;

class PersonLockedException extends \UnexpectedValueException
{
    protected $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function getPerson()
    {
        return $this->person;
    }
}