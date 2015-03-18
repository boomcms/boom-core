<?php

namespace Boom\Group;

use \DB as DB;

class Group
{
    /**
	 *
	 * @var \Model_Group
	 */
    protected $model;

    public function __construct(\Model_Group $model)
    {
        $this->model = $model;
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
        $role = \ORM::factory('Role', $roleId);

        if ( ! $role->loaded()) {
            throw new InvalidArgumentException("Argument 1 to ".__CLASS__."::".__METHOD__." must be a valid role ID. Called with $roleId which doesn't exist.");
        }

        // Check that the group doesn't already have this role before continuing.
        if ( ! $this->hasRole($roleId, $pageId)) {
            DB::insert('group_roles', ['group_id', 'role_id', 'allowed', 'page_id'])
                ->values([$this->getId(), $roleId, $allowed, $pageId])
                ->execute();

            // If the page ID is isn't set the set it to a string with '0' as the contents
            // otherwise it won't be included in the DB::select()
            if ($pageId) {
                DB::insert('people_roles', ['person_id', 'group_id', 'role_id', 'allowed', 'page_id'])
                    ->select(
                        DB::select('person_id', 'group_id', DB::expr($roleId), DB::expr($allowed), DB::expr($pageId))
                            ->from('people_groups')
                            ->where('group_id', '=', $this->getId())
                    )
                    ->execute();
            } else {
                DB::insert('people_roles', ['person_id', 'group_id', 'role_id', 'allowed'])
                    ->select(
                        DB::select('person_id', 'group_id', DB::expr($roleId), DB::expr($allowed))
                            ->from('people_groups')
                            ->where('group_id', '=', $this->getId())
                    )
                    ->execute();
            }
        }

        return $this;
    }

    public function delete()
    {
        $this->model->delete();

        return $this;
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function getName()
    {
        return $this->model->name;
    }

    public function getRoles($pageId = 0)
    {
        return DB::select('role_id', 'allowed')
            ->from('group_roles')
            ->where('group_id', '=', $this->getId())
            ->where('page_id', '=', $pageId)
            ->execute()
            ->as_array('role_id', 'allowed');
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

        $result = DB::select(DB::expr(1))
            ->from('group_roles')
            ->where('group_id', '=', $this->getId())
            ->where('role_id', '=', $role_id)
            ->where('page_id', '=', $page_id)
            ->execute()
            ->as_array();

        return (count($result) > 0);
    }

    public function loaded()
    {
        return $this->model->loaded();
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
        $this->model->remove('roles', $roleId);

        // Remove the role from people in this group.
        DB::delete('people_roles')
            ->where('group_id', '=', $this->getId())
            ->where('role_id', '=', $roleId)
            ->execute();

        return $this;
    }

    /**
	 *
	 * @return \Boom\Group\Group
	 */
    public function save()
    {
        $this->model->loaded() ? $this->model->update() : $this->model->create();

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Group\Group
	 */
    public function setName($name)
    {
        $this->model->name = $name;

        return $this;
    }
}
