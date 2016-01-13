<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Policies\BoomCMSPolicy;
use BoomCMS\Support\Facades\Router;
use Illuminate\Support\Facades\Auth;

class PagePolicy extends BoomCMSPolicy
{
    public function before(Person $person, $ability)
    {
        $result = parent::before($person, $ability);

        if ($result !== null) {
            return $result;
        }

        if (Auth::check('managePages', Router::getActiveSite())) {
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
