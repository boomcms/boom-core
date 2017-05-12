<?php

namespace BoomCMS\Policies;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Foundation\Policies\BoomCMSPolicy;
use BoomCMS\Support\Facades\Router;
use Illuminate\Support\Facades\Gate;

class PagePolicy extends BoomCMSPolicy
{
    public function add(Person $person, Page $page)
    {
        if ($this->managesPages()) {
            return true;
        }

        return $this->check('add', $person, $page);
    }

    public function edit(Person $person, Page $page)
    {
        if ($page->wasCreatedBy($person) || $this->managesPages()) {
            return true;
        }

        return $this->check('edit', $person, $page);
    }

    public function delete(Person $person, Page $page)
    {
        if ($page->wasCreatedBy($person) || $this->managesPages()) {
            return true;
        }

        return $this->check('delete', $person, $page);
    }

    public function check($role, Person $person, $page)
    {
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

    /**
     * Whether the page can be viewed.
     *
     * @param Person $person
     * @param Page   $page
     *
     * @return bool
     */
    public function view(Person $person, Page $page)
    {
        if (!$page->aclEnabled()) {
            return true;
        }

        if ($page->wasCreatedBy($person) || $this->managesPages()) {
            return true;
        }

        $aclGroupIds = $page->getAclGroupIds();

        if (count($aclGroupIds) === 0) {
            return true;
        }

        $groups = $person->getGroups();

        foreach ($groups as $group) {
            if ($aclGroupIds->contains($group->getId())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether the user has the 'managePages' role.
     *
     * @return bool
     */
    protected function managesPages()
    {
        return Gate::allows('managePages', Router::getActiveSite()) === true;
    }
}
