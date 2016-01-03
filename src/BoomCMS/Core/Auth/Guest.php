<?php

namespace BoomCMS\Core\Auth;

use BoomCMS\Contracts\Models\Group;
use BoomCMS\Contracts\Models\Person;

class Guest implements Person
{
    public function addGroup(Group $group)
    {
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function checkPersistCode($code)
    {
        return false;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getGroupIds()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getRememberToken()
    {
        return '';
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isSuperUser()
    {
        return false;
    }

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function removeGroup(Group $group)
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        return $this;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        return $this;
    }

    /**
     * @param string $token
     *
     * @return $this
     */
    public function setRememberToken($token)
    {
        return $this;
    }

    /**
     * @param bool $superuser
     *
     * @return $this
     */
    public function setSuperUser($superuser)
    {
        return $this;
    }
}
