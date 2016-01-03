<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Policies\BoomCMSPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class PagePolicy extends BoomCMSPolicy
{
    public function before(Person $person, $ability)
    {
        if ($person->isSuperuser() || Auth::check('managePages', Request::instance())) {
            return true;
        }
    }

    public function check($role, Person $person, $page)
    {
        if (in_array($role, ['edit', 'delete', 'editContent']) && $page->wasCreatedBy($person)) {
            return true;
        }

//        return parent::check($role, $person, $page);
    }
}
