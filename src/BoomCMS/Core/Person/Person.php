<?php

namespace BoomCMS\Core\Person;

use Boom\Group;
use Boom\Page\Page;

use Cartalyst\Sentry\Groups\GroupInterface;
use Cartalyst\Sentry\Users\UserInterface;

use \DB;
use \Model_Role as Role;

class Person implements UserInterface
{
    const LOCK_WAIT = 600;

   /**
    *
    * @var array
    */
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     *
     * @param GroupInterface   $group
     * @return \Boom\Person\Person
     */
    public function addGroup(GroupInterface $group)
    {
        if ($group->loaded()) {
            $this->model->add('groups', $group->getId());

            // Inherit any roles assigned to the group.
            DB::insert('people_roles', ['person_id', 'group_id', 'role_id', 'allowed', 'page_id'])
                ->select(
                    DB::select(DB::expr($this->getId()), DB::expr($group->getId()), 'role_id', 'allowed', 'page_id')
                        ->from('group_roles')
                        ->where('group_id', '=', $group->getId())
                    )
                ->execute();
        }

        return $this;
    }

    public function attemptActivation($activationCode)
    {

    }

    public function attemptResetPassword($resetCode, $newPassword)
    {

    }

    public function checkPassword($password)
    {

    }

    /**
     *
     * @param type $persistCode
     * @return boolean
     */
    public function checkPersistCode($persistCode)
    {
        return $persistCode === $this->getId();
    }

    public function checkResetPasswordCode($resetCode)
    {

    }

    public function clearResetPassword()
    {

    }

    public function delete()
    {
        if ($this->loaded()) {
            $this->model->delete();
        }

        return $this;
    }

    public function getActivationCode()
    {

    }

    public function getEmail()
    {
        return $this->data['email'];
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
        return $this->data['id'];
    }

    public function getLockedUntil()
    {
        return $this->data['locked_until'];
    }

    public function getLogin()
    {
        return $this->getEmail();
    }

    public function getLoginName()
    {
        return 'email';
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getPasswordName()
    {
        return 'password';
    }

    public function getPassword()
    {
        return $this->data['password'];
    }

    public function getPersistCode()
    {
        return $this->getId();
    }

    public function hasPagePermission(Role $role, Page $page)
    {
        $query = DB::select([DB::expr("bit_and(allowed)"), 'allowed'])
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

    public function hasPermission($role, $all = true)
    {
        $query = DB::select([DB::expr("bit_and(allowed)"), 'allowed'])
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

    /**
     * Always returns true because Boom doesn't require account activation
     *
     * @return boolean
     */
    public function isActivated()
    {
        return true;
    }

    public function isEnabled()
    {
        return (bool) $this->data['enabled'];
    }

    public function isLocked()
    {
        return $this->getLockedUntil() && ($this->getLockedUntil() > time());
    }

    public function inGroup(GroupInterface $group)
    {

    }

    /**
     * Always returns false as Boom doesn't currently have the concept of super users.
     *
     * This should be impelemented in the future.
     *
     * @return boolean
     */
    public function isSuperUser()
    {
        return false;
    }

    /**
     *
     * @return boolean
     */
    public function isValid()
    {
        return $this->getId() > 0;
    }

    public function loaded()
    {
        return $this->isValid();
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

    /**
     *
     * @param GroupInterface $group
     * @return \Boom\Person\Person
     */
    public function removeGroup(GroupInterface $group)
    {
        if ($group->loaded()) {
            $this->model->remove('groups', $group->getId());

            DB::delete('people_roles')
                ->where('group_id', '=', $group->getId())
                ->where('person_id', '=', $this->getId())
                ->execute();
        }

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
        $this->data['email'] = $email;

        return $this;
    }

    /**
	 *
	 * @param string $password
	 * @return \Boom\Person
	 */
    public function setEncryptedPassword($password)
    {
        $this->data['password'] = $password;

        return $this;
    }

    /**
	 *
	 * @param string $name
	 * @return \Boom\Person
	 */
    public function setName($name)
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function validate()
    {

    }

    public function getMergedPermissions()
    {

    }

    public function getPermissions()
    {

    }

    public function getResetPasswordCode()
    {

    }

    public function hasAccess($permissions, $all = true)
    {

    }

    public function hasAnyAccess(array $permissions)
    {

    }

    public function recordLogin()
    {

    }
}
