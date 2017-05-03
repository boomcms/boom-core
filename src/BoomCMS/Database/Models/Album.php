<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
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
     * {@inheritdoc}
     *
     * @param array $assetIds
     *
     * @return $this
     */
    public function addAssets(array $assetIds): AlbumInterface
    {
        $this->assets()->attach($assetIds);

        return $this->updateAssetCount();
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
     * {@inheritdoc}
     *
     * @param array $assetIds
     *
     * @return $this
     */
    public function removeAssets(array $assetIds): AlbumInterface
    {
        $this->assets()->detach($assetIds);

        return $this->updateAssetCount();
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

    /**
     * {@inheritdoc}
     *
     * @return AlbumInterface $this
     */
    public function updateAssetCount(): AlbumInterface
    {
        $this->update([
            self::ATTR_ASSET_COUNT => $this->assets->count(),
        ]);

        return $this;
    }
}
