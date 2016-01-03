<?php

namespace BoomCMS\Policies;

use BoomCMS\Foundation\Policies\BoomCMSPolicy;
use BoomCMS\Contracts\Models\Person;

class SitePolicy extends BoomCMSPolicy
{
    public function before(Person $person, $ability)
    {
        if ($person->isSuperuser()) {
            return true;
        }
    }
}
