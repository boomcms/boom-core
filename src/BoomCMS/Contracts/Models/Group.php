<?php

namespace BoomCMS\Contracts\Models;

interface Group
{
    public function addRole($roleId, $allowed, $pageId = 0);

    /**
     * @return int
     */
    public function getId();

    public function getName();

    public function getRoles();

    public function hasRole($roleId, $pageId = 0);

    public function removeRole($roleId);

    public function setName($name);
}