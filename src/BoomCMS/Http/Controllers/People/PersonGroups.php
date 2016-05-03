<?php

namespace BoomCMS\Http\Controllers\People;

use BoomCMS\Database\Models\Group as GroupModel;
use BoomCMS\Database\Models\Person as PersonModel;

class PersonGroups extends PeopleManager
{
    /**
     * Add the user to a group
     *
     * @param PersonModel $person
     * @param GroupModel $group
     */
    public function store(PersonModel $person, GroupModel $group)
    {
        $person->addGroup($group);
    }

    /**
     * Remove the user from a group
     *
     * @param PersonModel $person
     * @param GroupModel $group
     */
    public function destroy(PersonModel $person, GroupModel $group)
    {
        $person->removeGroup($group);
    }
}
