<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;

class SitePolicy
{
    public function before(Person $person, $ability)
    {
        if ($person->isSuperuser()) {
            return true;
        }
    }
}
