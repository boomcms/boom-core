<?php

namespace BoomCMS\Core\Group\Finder;

use BoomCMS\Core\Person\Person as PersonObject;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Person extends Filter
{
    protected $person;

    public function __construct(PersonObject $person)
    {
        $this->person = $person;
    }

    public function build(Builder $query)
    {
        return $query
            ->join('people_groups', 'group_id', '=', 'id')
            ->where('person_id', '=', $this->person->getId());
    }
}
