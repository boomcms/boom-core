<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Policies\BoomCMSPolicy;

class SitePolicy extends BoomCMSPolicy
{
    public function before(Person $person, $ability)
    {
        if ($person->isSuperuser()) {
            return true;
        }
    }
}
