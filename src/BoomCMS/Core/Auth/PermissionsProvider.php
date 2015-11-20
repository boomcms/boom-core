<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;

/**
 * This class is used to check whether a particular person can perform a particular role.
 *
 * Results of lookups are cached for the duration of the script execution to minimise database queries.
 */
class PermissionsProvider
{
    private $cache = [];

    public function lookup(Person $person, $role, Page $page = null)
    {
        return $page ? $this->lookupPagePermission($person, $role, $page) : $this->lookupPermission($person, $role);
    }

    public function lookupPagePermission(Person $person, $role, Page $page)
    {
        // Page permissions are prefixed with p_ so add the prefix if it's not present.
        if (substr($role, 0, 2) !== 'p_') {
            $role = 'p_'.$role;
        }

        do {
            $result = $this->doLookup($person, $role, $page->getId());

            if ($page->getParentId() === null) {
                break;
            }

            if ($result === null) {
                $page = $page->getParent();
            }
        } while ($result === null);

        return (bool) $result;
    }

    public function lookupPermission(Person $person, $role)
    {
        return (bool) $this->doLookup($person, $role, 0);
    }

    protected function doLookup(Person $person, $role, $pageId)
    {
        $hash = md5($person->getId().'-'.$role.'-'.$pageId);

        if (!isset($this->cache[$hash])) {
            $this->cache[$hash] = $person->isAllowed($role, $pageId);
        }

        return $this->cache[$hash];
    }
}
