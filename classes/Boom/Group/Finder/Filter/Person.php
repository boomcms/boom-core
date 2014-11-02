<?php

namespace Boom\Group\Finder\Filter;

use Boom\Person;

class Person extends \Boom\Finder\Filter
{
    protected $person;

    public function __construct(Person\Person $person)
    {
        $this->person = $person;
    }

    public function execute(\ORM $query)
    {
        return $query
            ->join('people_groups', 'inner')
            ->on('group_id', '=', 'id')
            ->where('person_id', '=', $this->person->getId());
    }
}
