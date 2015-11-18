<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Contracts\Models\Person;
use Exception;

class InvalidPasswordException extends Exception
{
    /**
     * @var Person
     */
    protected $person;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function getPerson()
    {
        return $this->person;
    }
}
