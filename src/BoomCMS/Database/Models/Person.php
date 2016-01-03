<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Group as GroupInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Support\Traits\Comparable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;

class Person extends Model implements PersonInterface, AuthenticatableContract, CanResetPassword
{
    use Authenticatable;
    use Comparable;
    use SoftDeletes;

    const ATTR_ID = 'id';
    const ATTR_NAME = 'name';
    const ATTR_EMAIL = 'email';
    const ATTR_ENABLED = 'enabled';
    const ATTR_PASSWORD = 'password';
    const ATTR_SUPERUSER = 'superuser';
    const ATTR_REMEMBER_TOKEN = 'remember_token';

    public $table = 'people';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * @param Group $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group)
    {
        $this->groups()->attach($group);

        return $this;
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

    public function getEmail()
    {
        return $this->{self::ATTR_EMAIL};
    }

    public function getEmailForPasswordReset()
    {
        return $this->getEmail();
    }

    public function getGroups()
    {
        return $this->groups()->get();
    }

    /**
     * @return array
     */
    public function getGroupIds()
    {
        return (array) $this->groups->lists('id');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return  (int) $this->{self::ATTR_ID};
    }

    public function getLogin()
    {
        return $this->getEmail();
    }

    public function getName()
    {
        return $this->{self::ATTR_NAME};
    }

    public function getPassword()
    {
        return $this->{self::ATTR_PASSWORD};
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->{self::ATTR_ENABLED} == true;
    }

    /**
     * @return bool
     */
    public function isSuperuser()
    {
        return $this->{self::ATTR_SUPERUSER} == true;
    }

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group)
    {
        $this->groups()->detach($group->getId());

        return $this;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->{self::ATTR_EMAIL} = $email;

        return $this;
    }

    public function setEmailAttribute($value)
    {
        $this->attributes[self::ATTR_EMAIL] = strtolower(trim($value));
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->{self::ATTR_ENABLED} = $enabled;

        return $this;
    }

    /**
     * @param string $password
     *
     * @return $this
     */
    public function setEncryptedPassword($password)
    {
        $this->{self::ATTR_PASSWORD} = $password;

        return $this;
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

    /**
     * @param bool $superuser
     *
     * @return $this
     */
    public function setSuperuser($superuser)
    {
        $this->{self::ATTR_SUPERUSER} = $superuser;

        return $this;
    }
}
