<?php

namespace BoomCMS\Foundation\Policies;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Contracts\Models\Person;
use BoomCMS\Support\Facades\Router;
use Illuminate\Support\Facades\DB;

abstract class BoomCMSPolicy
{
    protected $cache = [];

    public function __call($name, $arguments)
    {
        return $this->check($name, $arguments[0], $arguments[1]);
    }

    /**
     * @param Person $person
     * @param string $ability
     *
     * @return bool
     */
    public function before(Person $person, $ability)
    {
        if ($person->isSuperUser()) {
            return true;
        }

        if (!$person->hasSite(Router::getActiveSite())) {
            return false;
        }
    }

    public function check($role, Person $person, $where)
    {
        $hash = md5($role.'-'.$person->getId().'-'.$where);

        if (!isset($this->cache[$hash])) {
            $query = DB::table('group_role')
                ->select(DB::raw('bit_and(allowed) as allowed'))
                ->join('group_person', 'group_person.group_id', '=', 'group_role.group_id')
                ->join('groups', 'group_person.group_id', '=', 'groups.id')
                ->join('roles', 'roles.id', '=', 'group_role.role_id')
                ->whereNull('groups.deleted_at')
                ->where('group_person.person_id', '=', $person->getId())
                ->where('roles.name', '=', $role)
                ->groupBy('person_id');    // Strange results if this isn't here.

            if ($where instanceof Page) {
                $query->where('group_role.page_id', '=', $where->getId());
            }

            $result = $query->first();

            $this->cache[$hash] = (isset($result->allowed)) ? ($result->allowed === 1) : null;
        }

        return $this->cache[$hash];
    }
}
