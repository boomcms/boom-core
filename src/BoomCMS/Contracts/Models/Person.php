<?php

namespace BoomCMS\Contracts\Models;

interface Person
{
    public function addGroup(Group $group);

    public function checkPassword($password);

    public function checkPersistCode($code);

    public function getEmail();

    public function getGroups();

    public function getGroupIds();

    /**
     * @return int
     */
    public function getId();

    public function getName();

    public function getPassword();

    public function getRememberToken();

    public function isEnabled();

    public function isSuperUser();

    public function removeGroup(Group $group);

    public function setEmail($email);

    public function setEnabled($enabled);

    public function setName($name);

    public function setSuperUser($superuser);

    public function setRememberToken($token);
}
