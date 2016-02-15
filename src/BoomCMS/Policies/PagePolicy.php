<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Policies\BoomCMSPolicy;
use BoomCMS\Support\Facades\Router;
use Illuminate\Support\Facades\Gate;

class PagePolicy extends BoomCMSPolicy
{
    public function before(Person $person, $ability)
    {
        $result = parent::before($person, $ability);

        if ($result !== null) {
            return $result;
        }

        if (Gate::allows('managePages', Router::getActiveSite()) === true) {
            return true;
        }
    }

    public function check($role, Person $person, $page)
    {
        if (in_array($role, ['edit', 'delete', 'editContent']) && $page->wasCreatedBy($person)) {
            return true;
        }

        do {
            $result = parent::check($role, $person, $page);

            if ($page->getParentId() === null) {
                break;
            }

            if ($result === null) {
                $page = $page->getParent();
            }
        } while ($result === null && $page !== null);

        return (bool) $result;
    }
}
