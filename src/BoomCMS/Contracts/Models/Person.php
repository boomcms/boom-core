<?php

namespace BoomCMS\Contracts\Models;

interface Person
{
    public function addGroup(Group $group);

    public function checkPassword($password);

    public function checkPersistCode($code);

    public function getEmail();

    public function getFailedLogins();

    public function getGroups();

    public function getGroupIds();

    /**
     * @return int
     */
    public function getId();

    public function getLastFailedLogin();

    public function getLockedUntil();

    public function getName();

    public function getPassword();

    public function getRememberToken();

    public function incrementFailedLogins();

    /**
     * @param string $role
     * @param int    $pageId
     *
     * @return bool
     */
    public function isAllowed($role, $pageId);

    public function isEnabled();

    public function isLocked();

    public function isSuperUser();

    public function isValid();

    public function removeGroup(Group $group);

    public function setEmail($email);

    public function setEnabled($enabled);

    public function setLastFailedLogin($timestamp);

    public function setLockedUntil($timestamp);

    public function setName($name);

    public function setSuperUser($superuser);

    public function setRememberToken($token);
}
