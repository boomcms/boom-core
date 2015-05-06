<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Core\Page;
use BoomCMS\Core\Person;

use Illuminate\Support\Facades\DB;

/**
 * This class is used to check whether a particular person can perform a particular role.
 *
 * Results of lookups are cached for the duration of the script execution to minimise database queries.
 *
 */
class PermissionsProvider
{
    private $cache = [];

    public function lookup(Person\Person $person, $role, Page\Page $page = null)
    {
        return $page ? $this->lookupPagePermission($person, $page, $role) : $this->lookupPermission($person, $role);
    }

    public function lookupPagePermission(Person\Person $person, $role, Page\Page $page)
    {
        // Page permissions are prefixed with p_ so add the prefix if it's not present.
        if (substr($role, 0, 2) !== 'p_') {
            $role = 'p_' . $role;
        }

        $pageId = $pageId();

        do {
            $result = $this->doLookup($person->getId(), $role, $pageId);

            if ($result === null) {
                $pageId = $page->getParentId();
            }
        } while ($result === null && $pageId > 0);

        return (bool) $result;
    }

    public function lookupPermission(Person\Person $person, $role)
    {
        return (bool) $this->doLookup($person->getId(), $role, 0);
    }

    protected function doLookup($personId, $role, $pageId)
    {
        $hash = md5($personId . '-' . $role . '-' . $pageId);

        if ( ! isset($this->cache[$hash])) {
            $result = DB::table('people_roles')
                ->select(DB::raw("bit_and(allowed) as allowed"))
                ->where('person_id', '=', $personId)
                ->join('roles', 'roles.id', '=', 'people_roles.role_id')
                ->where('roles.name', '=', $role)
                ->groupBy('person_id')    // Strange results if this isn't here.
                ->where('people_roles.page_id', '=', $pageId)
                ->first();

            $result = isset($result[0]['allowed']) ? $result[0]['allowed'] : null;

            $this->cache[$hash] = $result;
        }

        return $this->cache[$hash];
    }
}
