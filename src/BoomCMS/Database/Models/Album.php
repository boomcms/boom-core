<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\SingleSiteInterface;
use BoomCMS\Foundation\Database\Model;
use BoomCMS\Support\Str;
use BoomCMS\Support\Traits\SingleSite;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model implements AlbumInterface, SingleSiteInterface
{
    use SingleSite;
    use SoftDeletes;

    const ATTR_NAME = 'name';
    const ATTR_DESCRIPTION = 'description';
    const ATTR_SLUG = 'slug';
    const ATTR_ORDER = 'order';
    const ATTR_ASSET_COUNT = 'asset_count';

    protected $table = 'albums';

    public $timestamps = true;

    /**
     * @param AssetInterface $asset
     *
     * @return $this
     */
    public function addAsset(AssetInterface $asset)
    {
        $this->assets()->attach($asset);
        $this->increment(self::ATTR_ASSET_COUNT);

        return $this;
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class);
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

    /**
     * @param AssetInterface $asset
     *
     * @return $this
     */
    public function removeAsset(AssetInterface $asset)
    {
        $this->assets()->detach($asset);
        $this->decrement(self::ATTR_ASSET_COUNT);

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
     * @param string $value
     */
    public function setNameAttribute($value)
    {
        $name = trim(strip_tags($value));

        $this->attributes[self::ATTR_NAME] = $name;
        $this->attributes[self::ATTR_SLUG] = Str::unique(Str::slug($name), function ($slug) {
            return !$this
                ->where(self::ATTR_ID, '!=', $this->getId())
                ->where(self::ATTR_SLUG, $slug)
                ->exists();
        });
    }
}
