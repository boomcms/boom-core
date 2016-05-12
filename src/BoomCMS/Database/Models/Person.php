<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Auth\Hasher;
use BoomCMS\Contracts\Models\Group as GroupInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Support\Traits\Comparable;
use BoomCMS\Support\Traits\MultipleSites;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;

class Person extends Model implements PersonInterface, AuthenticatableContract, CanResetPassword
{
    use Authenticatable;
    use Authorizable;
    use Comparable;
    use SoftDeletes;
    use MultipleSites;

    const ATTR_ID = 'id';
    const ATTR_NAME = 'name';
    const ATTR_EMAIL = 'email';
    const ATTR_ENABLED = 'enabled';
    const ATTR_PASSWORD = 'password';
    const ATTR_SUPERUSER = 'superuser';
    const ATTR_REMEMBER_TOKEN = 'remember_token';
    const ATTR_LAST_LOGIN = 'last_login';

    public $table = 'people';

    protected $guard = 'boomcms';

    public $guarded = [
        self::ATTR_ID,
        self::ATTR_LAST_LOGIN,
    ];

    protected $casts = [
        self::ATTR_ENABLED    => 'boolean',
        self::ATTR_ID         => 'integer',
        self::ATTR_LAST_LOGIN => 'datetime',
        self::ATTR_SUPERUSER  => 'boolean',
    ];

	protected $hidden = [
		self::ATTR_PASSWORD,
		self::ATTR_REMEMBER_TOKEN,
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

    public function checkPassword($password)
    {
        return (new Hasher())->check($password, $this->getPassword());
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
     * @return int
     */
    public function getId()
    {
        return $this->{self::ATTR_ID};
    }

    /**
     * Returns the time of the user's last login.
     *
     * @return null|Carbon
     */
    public function getLastLogin()
    {
        return $this->{self::ATTR_LAST_LOGIN};
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
     * Whether the person has ever logged in.
     *
     * @return bool
     */
    public function hasLoggedIn()
    {
        return $this->{self::ATTR_LAST_LOGIN} !== null;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->{self::ATTR_ENABLED} === true;
    }

    /**
     * @return bool
     */
    public function isSuperuser()
    {
        return $this->{self::ATTR_SUPERUSER} === true;
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
     * @param Builder       $query
     * @param SiteInterface $site
     *
     * @return Buider
     */
    public function scopeWhereSite(Builder $query, SiteInterface $site)
    {
        return $query
            ->join('person_site', 'people.id', '=', 'person_site.person_id')
            ->where('person_site.site_id', '=', $site->getId());
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
     * Set the time the user last logged in.
     *
     * @param Carbon $time
     *
     * @return $this
     */
    public function setLastLogin(Carbon $time)
    {
        $this->{self::ATTR_LAST_LOGIN} = $time;

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
