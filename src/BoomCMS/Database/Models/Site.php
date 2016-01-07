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


}
