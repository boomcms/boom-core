<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Group as GroupInterface;
use BoomCMS\Support\Traits\Comparable;
use BoomCMS\Support\Traits\SingleSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model implements GroupInterface
{
    use Comparable;
    use SingleSite;
    use SoftDeletes;

    const ATTR_ID = 'id';
    const ATTR_NAME = 'name';
    const ATTR_SITE = 'site_id';
    const PIVOT_ATTR_ID = 'id';
    const PIVOT_ATTR_PAGE_ID = 'page_id';
    const PIVOT_ATTR_ROLE_ID = 'role_id';
    const PIVOT_ATTR_ALLOWED = 'allowed';

    protected $table = 'groups';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * Adds a role to the current group.
     *
     * This will also add the role to all members of the group.
     *
     *
     * @param int $roleId  ID of the role to add
     * @param int $allowed Whether the group is allowed or prevented from the role.
     * @param int $pageId  Make the role active at a particular point in the page tree.
     *
     * @return $this
     */
    public function addRole($roleId, $allowed, $pageId = 0)
    {
        if (!$this->hasRole($roleId, $pageId)) {
            $this->roles()
                ->attach($roleId, [
                    self::PIVOT_ATTR_ALLOWED => $allowed,
                    self::PIVOT_ATTR_PAGE_ID => $pageId,
                ]);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return  (int) $this->{self::ATTR_ID};
    }

    public function getName()
    {
        return $this->{self::ATTR_NAME};
    }

    public function getRoles($pageId = 0)
    {
        return $this->roles()
            ->wherePivot(self::PIVOT_ATTR_PAGE_ID, '=', $pageId)
            ->select(self::PIVOT_ATTR_ID, self::PIVOT_ATTR_ALLOWED)
            ->get();
    }

    /**
     * Determines whether the current group has a specified role allowed / denied.
     *
     * @param int $roleId
     * @param int $pageId
     *
     * @return bool
     */
    public function hasRole($roleId, $pageId = 0)
    {
        return $this->roles()
            ->wherePivot(self::PIVOT_ATTR_PAGE_ID, '=', $pageId)
            ->wherePivot(self::PIVOT_ATTR_ROLE_ID, '=', $roleId)
            ->exists();
    }

    /**
     * Remove a role from a group.
     *
     * After removing the role from the group the permissions for the people the group are updated.
     *
     * @param int $roleId
     *
     * @return $this
     */
    public function removeRole($roleId, $pageId = 0)
    {
        $this->roles()
            ->wherePivot(self::PIVOT_ATTR_ROLE_ID, '=', $roleId)
            ->wherePivot(self::PIVOT_ATTR_PAGE_ID, '=', $pageId)
            ->detach();

        return $this;
    }

    /**
     * @return $this
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes[self::ATTR_NAME] = trim(strip_tags($value));
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->{self::ATTR_NAME} = $name;

        return $this;
    }
}
