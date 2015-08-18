<?php

namespace BoomCMS\Core\Group\Finder;

use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Core\Person\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ExcludingPersonsGroups extends Filter
{
    /**
     * @var Person
     */
    private $person;

    /**
     * @param Person $person
     */
    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function build(Builder $query)
    {
        $query->whereNotIn('id',
            DB::table('people_groups')
                ->select('group_id')
                ->where('person_id', '=', $this->person->getId())
                ->lists('group_id')
        );

        return $query;
    }
}
