<?php

namespace BoomCMS\Core\Group\Finder\Filter;

class Person extends \Boom\Finder\Filter
{
    protected $person;

    public function __construct(\Boom\Person\Person $person)
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
