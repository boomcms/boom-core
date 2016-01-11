<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Support\Traits\Comparable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model implements SiteInterface
{
    use Comparable;
    use SoftDeletes;

    const ATTR_ID = 'id';
    const ATTR_ADMIN_EMAIL = 'admin_email';
    const ATTR_ANALYTICS = 'analytics';
    const ATTR_HOSTNAME = 'hostname';
    const ATTR_NAME = 'name';
    const ATTR_DEFAULT = 'default';

    protected $casts = [
        self::ATTR_ID      => 'integer',
        self::ATTR_DEFAULT => 'boolean',
    ];

    protected $table = 'sites';

    public $guarded = [
        self::ATTR_ID,
    ];

    /**
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->{self::ATTR_ADMIN_EMAIL};
    }

    /**
     * @return string
     */
    public function getAnalytics()
    {
        return $this->{self::ATTR_ANALYTICS};
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->{self::ATTR_HOSTNAME};
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->{self::ATTR_ID};
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->{self::ATTR_NAME};
    }

    /**
     * @return bool
     */
    public function isDefault()
    {
        return $this->{self::ATTR_DEFAULT} === true;
    }

    /**
     * @param type $email
     *
     * @return $this
     */
    public function setAdminEmail($email)
    {
        $this->{self::ATTR_ADMIN_EMAIL} = $email;

        return $this;
    }

    /**
     * @param type $code
     *
     * @return $this
     */
    public function setAnalytics($code)
    {
        $this->{self::ATTR_ANALYTICS} = $code;

        return $this;
    }

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->{self::ATTR_DEFAULT} = ($default == true);

        return $this;
    }

    /**
     * @param type $hostname
     *
     * @return $this
     */
    public function setHostname($hostname)
    {
        $this->{self::ATTR_HOSTNAME} = $hostname;

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
}
