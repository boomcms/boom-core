<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group;
use BoomCMS\Database\Models\Person;

class PersonGroups extends PeopleManager
{
    /**
     * Add the user to a group.
     *
     * @param Person $person
     * @param Group  $group
     */
    public function store(Person $person, Group $group)
    {
        $person->addGroup($group);
    }

    /**
     * Remove the user from a group.
     *
     * @param Person $person
     * @param Group  $group
     */
    public function destroy(Person $person, Group $group)
    {
        $person->removeGroup($group);
    }
}
