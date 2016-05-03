<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Database\Models\Person;

class PersonGroups extends PeopleManager
{
    /**
     * Add the user to a group.
     *
     * @param Person     $person
     * @param GroupModel $group
     */
    public function store(Person $person, GroupModel $group)
    {
        $person->addGroup($group);
    }

    /**
     * Remove the user from a group.
     *
     * @param Person     $person
     * @param GroupModel $group
     */
    public function destroy(Person $person, GroupModel $group)
    {
        $person->removeGroup($group);
    }
}
