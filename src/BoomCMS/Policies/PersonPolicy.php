<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;

class PersonPolicy
{
    /**
     * Whether a user can edit the superuser status of another
     *
     * @param Person $user
     * @param Person $editing
     *
     * @return bool
     */
    public function editSuperuser(Person $user, Person $editing)
    {
        return $user->isSuperUser() && !$user->is($editing);
    }
}
