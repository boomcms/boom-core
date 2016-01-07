<?php

namespace BoomCMS\Contracts\Models;

interface Person
{
    public function addGroup(Group $group);

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function addSite(Site $site);

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

    /**
     * @return array
     */
    public function getSites();

    public function isEnabled();

    public function isSuperUser();

    public function removeGroup(Group $group);

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function removeSite(Site $site);

    public function setEmail($email);

    public function setEnabled($enabled);

    public function setName($name);

    public function setSuperUser($superuser);

    public function setRememberToken($token);
}
