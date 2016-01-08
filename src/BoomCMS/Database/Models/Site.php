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

    protected $casts = [
        self::ATTR_ID => 'integer',
    ];

    protected $table = 'sites';

    public $guarded = [
        self::ATTR_ID,
    ];


    public function getAdminEmail()
    {
        
    }

    public function getAnalytics()
    {
        
    }

    public function getHostname()
    {
        
    }

    public function getId()
    {
        
    }

    public function getName()
    {
        
    }

    public function setAdminEmail($email)
    {
        
    }

    public function setAnalytics($code)
    {
        
    }

    public function setHostname($hostname)
    {
        
    }

    public function setName($name)
    {
        
    }
}
