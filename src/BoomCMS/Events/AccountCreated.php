<?php

namespace BoomCMS\Events;

use BoomCMS\Contracts\Models\Person;

class AccountCreated
{
    /**
     * @var Person
     */
    protected $createdBy;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var Person
     */
    protected $person;

    public function __construct(Person $person, $password, Person $createdBy = null)
    {
        $this->createdBy = $createdBy;
        $this->password = $password;
        $this->person = $person;
    }

    /**
     * @return Person
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}
