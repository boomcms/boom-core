<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Support\Traits\Comparable;
use BoomCMS\Support\Traits\SingleSite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model implements TagInterface
{
    use Comparable;
    use SingleSite;

    const ATTR_ID = 'id';
    const ATTR_GROUP = 'group';
    const ATTR_NAME = 'name';
    const ATTR_SLUG = 'slug';

    protected $table = 'tags';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->{self::ATTR_GROUP};
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
     * @return string
     */
    public function getSlug()
    {
        return $this->{self::ATTR_SLUG};
    }

    public function scopeAppliedToALivePage($query)
    {
        return $query
            ->join('pages_tags as pt1', 'tags.id', '=', 'pt1.tag_id')
            ->leftJoin('pages as p1', 'pt1.page_id', '=', 'p1.id')
            ->whereNull('p1.deleted_at')
            ->groupBy('tags.id');
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
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        $name = trim(strip_tags($value));

        $this->attributes[self::ATTR_NAME] = $name;
        $this->attributes[self::ATTR_SLUG] = Str::slug($name);
    }
}
