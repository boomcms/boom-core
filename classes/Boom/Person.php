<?php

namespace Boom;

use Boom\Page\Page;

use \DB;
use \Model_Role as Role;

class Person
{
    /**
	 *
	 * @var \Model_Person
	 */
    protected $model;

    public function __construct(\Model_Person $model)
    {
        $this->model = $model;
    }

    public function addGroup(Group\Group $group)
    {
        $this->model->add('groups', $group->getId());

        // Inherit any roles assigned to the group.
        DB::insert('people_roles', array('person_id', 'group_id', 'role_id', 'allowed', 'page_id'))
            ->select(
                DB::select(DB::expr($this->getId()), DB::expr($group->getId()), 'role_id', 'allowed', 'page_id')
                    ->from('group_roles')
                    ->where('group_id', '=', $group->getId())
                )
            ->execute();

        return $this;
    }

    public function delete()
    {
        if ($this->loaded()) {
            $this->model->delete();
        }

        return $this;
    }

    public function getEmail()
    {
        return $this->model->email;
    }

    public function getGroups()
    {
        $finder = new Group\Finder();

        return $finder
            ->addFilter(new Group\Finder\Filter\Person($this))
            ->findAll();
    }

    public function getId()
    {
        return $this->model->id;
    }

    public function getLockedUntil()
    {
        return $this->model->locked_until;
    }

    public function getName()
    {
        return $this->model->name;
    }

    public function getPassword()
    {
        return $this->model->password;
    }

    public function hasPagePermission(Role $role, Page $page)
    {
        $query = DB::select(array(DB::expr("bit_and(allowed)"), 'allowed'))
            ->from('people_roles')
            ->where('person_id', '=', $this->getId())
            ->where('role_id', '=', $role->id)
            ->group_by('person_id')    // Strange results if this isn't here.
            ->join('page_mptt', 'left')
            ->on('people_roles.page_id', '=', 'page_mptt.id')
            ->where('lft', '<=', $page->getMptt()->lft)
            ->where('rgt', '>=', $page->getMptt()->rgt)
            ->where('scope', '=', $page->getMptt()->scope);

        $result = $query
            ->execute()
            ->as_array();

        return  ( ! empty($result) && (boolean) $result[0]['allowed']);
    }

    public function hasPermission(Role $role)
    {
        $query = DB::select(array(DB::expr("bit_and(allowed)"), 'allowed'))
            ->from('people_roles')
            ->where('person_id', '=', $this->getId())
            ->where('role_id', '=', $role->id)
            ->group_by('person_id')    // Strange results if this isn't here.
            ->where('people_roles.page_id', '=', 0);

        $result = $query
            ->execute()
            ->as_array();

        return  ( ! empty($result) && (boolean) $result[0]['allowed']);
    }

    public function isEnabled()
    {
        return (bool) $this->model->enabled;
    }

    public function isLocked()
    {
        return $this->getLockedUntil() && ($this->getLockedUntil() > $_SERVER['REQUEST_TIME']);
    }

    public function loaded()
    {
        return $this->model->loaded();
    }

    public function loginFailed()
    {
        $this->model->set('failed_logins', ++$this->model->failed_logins);

        if ($this->model->failed_logins > 3) {
            $this->model->set('locked_until', time() + static::LOCK_WAIT);
        }

        $this->model->update();

        return $this;
    }

    public function removeGroup(Group\Group $group)
    {
        $this->model->remove('groups', $group->getId());

        DB::delete('people_roles')
            ->where('group_id', '=', $group->getId())
            ->where('person_id', '=', $this->getId())
            ->execute();

        return $this;
    }

    /**
	 *
	 * @return \Boom\Person
	 */
    public function save()
    {
        $this->model->loaded() ? $this->model->update() : $this->model->create();

        return $this;
    }

    /**
	 *
	 * @param string $email
	 * @return \Boom\Person
	 */
    public function setEmail($email)
    {
        $this->model->email = $email;

        return $this;
    }

    /**
	 *
	 * @param string $password
	 * @return \Boom\Person
	 */
    public function setEncryptedPassword($password)
    {
        $this->model->password = $password;

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Person
	 */
    public function setName($name)
    {
        $this->model->name = $name;

        return $this;
    }
}
