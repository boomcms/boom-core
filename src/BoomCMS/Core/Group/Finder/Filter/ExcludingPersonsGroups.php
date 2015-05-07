<?php

namespace BoomCMS\Core\Group\Finder\Filter;

use BoomCMS\Core\Person;
use DB;
use ORM;

class ExcludingPersonsGroups extends \Boom\Finder\Filter
{
    /**
     *
     * @var \Boom\Person\Person
     */
    private $person;

    /**
     *
     * @param \Boom\Person\Person $person
     */
    public function __construct(Person\Person $person)
    {
        $this->person = $person;
    }

    public function execute(ORM $query)
    {
        $query->where('id', 'NOT IN',
            DB::select('group_id')
                ->from('people_groups')
                ->where('person_id', '=', $this->person->getId())
        );

        return $query;
    }
}
