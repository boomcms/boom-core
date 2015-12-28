<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;

class PagePolicy
{
    public function before(Person $person, $ability)
    {
        if ($person->isSuperuser()) {
            return true;
        }
    }

    public function __call($name, $arguments)
    {
        dd($name, $arguments);
    }
}
