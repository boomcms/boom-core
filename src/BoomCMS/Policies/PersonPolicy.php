<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;

class PersonPolicy
{
    public function editSuperuser(Person $user, Person $editing)
    {
        return $user->isSuperUser() && $user->getId() !== $editing->getId();
    }
}