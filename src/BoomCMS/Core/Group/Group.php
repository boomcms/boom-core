<?php

namespace BoomCMS\Core\Group;

use Illuminate\Support\Facades\DB;

class Group
{
    /**
	 *
	 * @var array
	 */
    protected $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
	 * Adds a role to the current group.
	 *
	 * This will also add the role to all members of the group.
	 *
	 *
	 * @param integer $roleId	ID of the role to add
	 * @param integer $allowed	Whether the group is allowed or prevented from the role.
	 * @param integer $pageId	Make the role active at a particular point in the page tree.
	 *
	 * @throws InvalidArgumentException When called with a role ID which is not in use.
	 *
	 * @return \Boom\Group\Group
	 */
    public function addRole($roleId, $allowed, $pageId = 0)
    {
        if ( ! $this->hasRole($roleId, $pageId)) {
            DB::table('group_roles')
				->insert([
					'group_id' => $this->getId(),
					'role_id' => $roleId,
					'allowed' => $allowed,
					'page_id' => $pageId
				]);

            if ($pageId) {
                DB::table('people_roles')
					->insert(
						DB::table('people_groups')
							->select('person_id', 'group_id', DB::raw("'$roleId'"), DB::raw("'$allowed'"), DB::raw("'$pageId'"))
                            ->where('group_id', '=', $this->getId())
							->get()
                    );
            } else {
                DB::table('people_roles')
					->insert(
						DB::table('people_groups')
							->select('person_id', 'group_id', DB::raw("'$roleId'"), DB::raw("'$allowed'"))
                            ->where('group_id', '=', $this->getId())
							->get()
                    );
            }
        }

        return $this;
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getRoles($pageId = 0)
    {
        return DB::table('group_roles')
			->select('role_id', 'allowed')
            ->where('group_id', '=', $this->getId())
            ->where('page_id', '=', $pageId)
			->get();
    }

    /**
	 * Determines whether the current group has a specified role allowed / denied.
	 *
	 * @param integer $role_id
	 * @param integer $page_id
	 * @return boolean
	 */
    public function hasRole($role_id, $page_id = 0)
    {
        if ( ! $this->loaded()) {
            return false;
        }

        $result = DB::table('group_roles')
            ->select(DB::raw(1))
            ->where('group_id', '=', $this->getId())
            ->where('role_id', '=', $role_id)
            ->where('page_id', '=', $page_id)
            ->get();

        return (count($result) > 0);
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    /**
	 * Remove a role from a group.
	 *
	 * After removing the role from the group the permissions for the people the group are updated.
	 *
	 * @param integer $roleId
	 * @return \Boom\Group\Group
	 */
    public function removeRole($roleId)
    {
		DB::table('group_roles')
			->where('group_id', '=', $this->getId())
			->where('role_id', '=', $roleId)
			->delete();

        // Remove the role from people in this group.
        DB::table('people_roles')
            ->where('group_id', '=', $this->getId())
            ->where('role_id', '=', $roleId)
            ->delete();

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Group\Group
	 */
    public function setName($name)
    {
        $this->attributes['name'] = $name;

        return $this;
    }
}
