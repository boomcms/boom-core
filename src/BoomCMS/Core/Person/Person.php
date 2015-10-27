<?php

namespace BoomCMS\Core\Person;

use BoomCMS\Core\Group;
use BoomCMS\Support\Traits\Comparable;
use DateTime;
use Hautelook\Phpass\PasswordHash;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\DB;

class Person implements Arrayable, CanResetPassword
{
    use Comparable;

    /**
     * @var array
     */
    protected $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param Group\Group $group
     *
     * @return Person
     */
    public function addGroup(Group\Group $group)
    {
        if ($this->loaded() && $group->loaded()) {
            DB::table('people_groups')
                ->insert([
                    'person_id' => $this->getId(),
                    'group_id'  => $group->getId(),
                ]);

            // Inherit any roles assigned to the group.
            $select = DB::table('group_roles')
                ->select(DB::raw($this->getId()), DB::raw($group->getId()), 'role_id', 'allowed', 'page_id')
                ->where('group_id', '=', $group->getId());

            $bindings = $select->getBindings();
            $insert = 'INSERT INTO people_roles (person_id, group_id, role_id, allowed, page_id) '.$select->toSql();

            DB::statement($insert, $bindings);
        }

        return $this;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function checkPassword($password)
    {
        $hasher = new PasswordHash(8, false);

        return $hasher->checkPassword($password, $this->getPassword());
    }

    /**
     * @param type $persistCode
     *
     * @return bool
     */
    public function checkPersistCode($persistCode)
    {
        return $persistCode === $this->getId();
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function getEmail()
    {
        return $this->get('email');
    }

    public function getEmailForPasswordReset()
    {
        return $this->getEmail();
    }

    public function getFailedLogins()
    {
        return $this->get('failed_logins');
    }

    public function getGroups()
    {
        $finder = new Group\Finder\Finder();

        return $finder
            ->addFilter(new Group\Finder\Person($this))
            ->findAll();
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getLastFailedLogin()
    {
        $time = new DateTime();

        if ($timestamp = $this->get('last_failed_login')) {
            $time->setTimestamp($timestamp);
        }

        return $time;
    }

    public function getLockedUntil()
    {
        return $this->get('locked_until');
    }

    public function getLogin()
    {
        return $this->getEmail();
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getPassword()
    {
        return $this->get('password');
    }

    public function getRememberToken()
    {
        return $this->get('remember_token');
    }

    public function incrementFailedLogins()
    {
        ++$this->data['failed_logins'];

        return $this;
    }

    public function isEnabled()
    {
        return $this->get('enabled') == true;
    }

    public function isLocked()
    {
        return $this->getLockedUntil() && ($this->getLockedUntil() > time());
    }

    public function isSuperuser()
    {
        return $this->get('superuser') == true;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->loaded() && !$this->isLocked();
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    /**
     * @param Group $group
     *
     * @return Person
     */
    public function removeGroup(Group\Group $group)
    {
        if ($group->loaded() && $this->loaded()) {
            DB::table('people_groups')
                ->where('person_id', '=', $this->getId())
                ->where('group_id', '=', $group->getId())
                ->delete();

            DB::table('people_roles')
                ->where('person_id', '=', $this->getId())
                ->where('group_id', '=', $group->getId())
                ->delete();
        }

        return $this;
    }

    /**
     * @param string $email
     *
     * @return \Boom\Person
     */
    public function setEmail($email)
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function setEnabled($enabled)
    {
        $this->data['enabled'] = $enabled;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return \Boom\Person
     */
    public function setEncryptedPassword($password)
    {
        $this->data['password'] = $password;

        return $this;
    }

    public function setFailedLogins($count)
    {
        $this->data['failed_logins'] = $count;

        return $this;
    }

    public function setLastFailedLogin($time)
    {
        $this->data['last_failed_login'] = $time;

        return $this;
    }

    public function setLockedUntil($timestamp)
    {
        $this->data['locked_until'] = $timestamp;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return \Boom\Person
     */
    public function setName($name)
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function setSuperuser($superuser)
    {
        $this->data['superuser'] = $superuser;

        return $this;
    }

    /**
     * @param string $token
     *
     * @return \BoomCMS\Core\Person\Person
     */
    public function setRememberToken($token)
    {
        $this->data['remember_token'] = $token;

        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }
}
