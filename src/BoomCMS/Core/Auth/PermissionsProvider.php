<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Core\Page;
use BoomCMS\Core\Person;

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

        $hash = $this->getHash($person->getId, $role, $page->getId());

        if ( ! isset($this->cache[$hash])) {
            $result = DB::table('people_roles')
                ->select(DB::raw("bit_and(allowed) as allowed"))
                ->where('person_id', '=', $this->getId())
                ->join('roles', 'inner')
                ->on('roles.id', '=', 'people_roles.role_id')
                ->where('roles.name', '=', $role)
                ->where('people_roles.page_id', '=', $page->getId())
                ->group_by('person_id')    // Strange results if this isn't here.
                ->first();

            $result = ( ! empty($result) && (boolean) $result[0]['allowed']);

            $this->cache[$hash] = $result;
        }

        return $this->cache[$hash];
    }


    public function lookupPermission(Person\Person $person, $role)
    {
        $hash = $this->getHash($person->getId(), $role);

        if ( ! isset($this->cache[$hash])) {
            $result = DB::table('people_roles')
                ->select(DB::raw("bit_and(allowed) as allowed"))
                ->where('person_id', '=', $this->getId())
                ->join('roles', 'inner')
                ->on('roles.id', '=', 'people_roles.role_id')
                ->where('roles.name', '=', $role)
                ->group_by('person_id')    // Strange results if this isn't here.
                ->where('people_roles.page_id', '=', 0)
                ->first();

            $result = ( ! empty($result) && (boolean) $result[0]['allowed']);

            $this->cache[$hash] = $result;
        }

        return $this->cache[$hash];
    }

    protected function getHash($personId, $role, $pageId)
    {
        return md5($personId . '-' . $role . '-' . $pageId);
    }
}