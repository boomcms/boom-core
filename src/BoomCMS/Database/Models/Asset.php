<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Album as AlbumInterface;
use BoomCMS\Contracts\Models\Asset as AssetInterface;
use BoomCMS\Contracts\Models\Person as PersonInterface;
use BoomCMS\Contracts\SingleSiteInterface;
use BoomCMS\Foundation\Database\Model;
use BoomCMS\Support\Str;
use BoomCMS\Support\Traits\SingleSite;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

class Asset extends Model implements AssetInterface, SingleSiteInterface
{
    use SingleSite;

    const ATTR_TITLE = 'title';
    const ATTR_DESCRIPTION = 'description';
    const ATTR_TYPE = 'type';
    const ATTR_CREATED_BY = 'created_by';
    const ATTR_UPLOADED_AT = 'created_at';
    const ATTR_THUMBNAIL_ID = 'thumbnail_asset_id';
    const ATTR_CREDITS = 'credits';
    const ATTR_DOWNLOADS = 'downloads';
    const ATTR_PUBLISHED_AT = 'published_at';
    const ATTR_PUBLIC = 'public';

    public $table = 'assets';

    /**
     * @var PersinInterface
     */
    protected $uploadedBy;

    /**
     * @var AssetVersion
     */
    protected $latestVersion;

    protected $attributes = [
        self::ATTR_PUBLIC => true,
    ];

    protected $casts = [
        self::ATTR_PUBLISHED_AT => 'datetime',
        self::ATTR_PUBLIC       => 'boolean',
    ];

    protected $appends = [
        'readable_filesize',
        'metadata',
    ];

    protected $versionColumns = [
        'asset_id'           => '',
        'width'              => '',
        'height'             => '',
        'filesize'           => '',
        'filename'           => '',
        'version:created_at' => '',
        'version:created_by' => '',
        'version:id'         => '',
        'extension'          => '',
    ];

    public function albums()
    {
        return $this->belongsToMany(Album::class);
    }

    /**
     * @return float
     */
    public function getAspectRatio()
    {
        return $this->getLatestVersion()->getAspectRatio();
    }

    /**
     * @return string
     */
    public function getCredits()
    {
        return $this->{self::ATTR_CREDITS};
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->{self::ATTR_DESCRIPTION};
    }

    /**
     * @return int
     */
    public function getDownloads()
    {
        return (int) $this->{self::ATTR_DOWNLOADS};
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->getLatestVersion()->getExtension();
    }

    /**
     * @return int
     */
    public function getFilesize()
    {
        return $this->getLatestVersion()->getFilesize();
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->getLatestVersion()->getHeight();
    }

    /**
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->getLatestVersion()->getEditedAt();
    }

    /**
     * @return AssetVersion
     */
    public function getLatestVersion()
    {
        if ($this->latestVersion === null) {
            $this->latestVersion = $this->versions()
                ->orderBy(AssetVersion::ATTR_ID, 'desc')
                ->first();
        }

        return $this->latestVersion;
    }

    public function getLatestVersionId()
    {
        return $this->getLatestVersion()->getId();
    }

    public function getMetadataAttribute()
    {
        return $this->getLatestVersion()->getMetadata();
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->getLatestVersion()->getMimetype();
    }

    /**
     * Returns the published_at property.
     *
     * @return Carbon
     */
    public function getPublishedAt(): Carbon
    {
        return $this->{self::ATTR_PUBLISHED_AT};
    }

    public function getOriginalFilename()
    {
        return str_replace(['\\', '/'], '', $this->getLatestVersion()->getFilename());
    }

    public function getReadableFilesizeAttribute()
    {
        return Str::filesize($this->getFilesize());
    }

    /**
     * @return int
     */
    public function getThumbnailAssetId()
    {
        return (int) $this->{self::ATTR_THUMBNAIL_ID};
    }

    public function getThumbnail()
    {
        return $this->belongsTo(static::class, 'thumbnail_asset_id', 'id')->first();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->{self::ATTR_TITLE};
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->{self::ATTR_TYPE};
    }

    /**
     * @return PersonInterface
     */
    public function getUploadedBy()
    {
        if ($this->uploadedBy === null) {
            $this->uploadedBy = $this->uploadedBy()->first();
        }

        return $this->uploadedBy;
    }

    public function getUploadedTime()
    {
        return (new DateTime())->setTimestamp($this->{self::ATTR_UPLOADED_AT});
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->getLatestVersion()->getWidth();
    }

    public function hasThumbnail()
    {
        return $this->getThumbnailAssetId() > 0;
    }

    /**
     * @return $this
     */
    public function incrementDownloads()
    {
        $this->increment(self::ATTR_DOWNLOADS);

        return $this;
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        return $this->getType() === 'image';
    }

    /**
     * Whether the asset is public (visible to users who aren't logged in to the CMS).
     *
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->{self::ATTR_PUBLIC} === true;
    }

    /**
     * @return bool
     */
    public function isVideo()
    {
        return $this->getType() === 'video';
    }

    public function scopeWhereAlbum(Builder $query, $albums): Builder
    {
        $albumIds = $albums instanceof AlbumInterface ? [$albums->getId()] : $albums->map(function ($album) {
            return $album->getId();
        });

        return $query
            ->whereHas('albums', function (Builder $query) use ($albumIds) {
                $query->whereIn('albums.id', $albumIds);
            });
    }

    /**
     * @param string $credits
     *
     * @return $this
     */
    public function setCredits($credits)
    {
        $this->{self::ATTR_CREDITS} = $credits;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->{self::ATTR_DESCRIPTION} = $description;

        return $this;
    }

    public function setPublishedAt($when = null)
    {
        if ($when !== null && !is_object($when)) {
            $when = new DateTime($when);
        }

        $this->{self::ATTR_PUBLISHED_AT} = $when;

        return $this;
    }

    /**
     * @param int $assetId
     *
     * @return $this
     */
    public function setThumbnailAssetId($assetId)
    {
        $this->{self::ATTR_THUMBNAIL_ID} = $assetId;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->{self::ATTR_TITLE} = $title;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->{self::ATTR_TYPE} = $type;

        return $this;
    }

    /**
     * Set the version to use with the asset.
     *
     * @param AssetVersion $version
     *
     * @return $this
     */
    public function setVersion(AssetVersion $version)
    {
        $this->latestVersion = $version;

        return $this;
    }

    public function scopeWithLatestVersion(Builder $query)
    {
        return $query
            ->select('assets.*')
            ->join('asset_versions as version', 'assets.id', '=', 'version.asset_id')
            ->leftJoin('asset_versions as av2', function (JoinClause $query) {
                $query
                    ->on('av2.asset_id', '=', 'version.asset_id')
                    ->on('av2.id', '>', 'version.id');
            })
            ->whereNull('av2.id');
    }

    public function uploadedBy()
    {
        return $this->hasOne(Person::class, 'id', 'created_by');
    }

    public function versions()
    {
        return $this->hasMany(AssetVersion::class)
            ->orderBy(AssetVersion::ATTR_CREATED_AT, 'desc');
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        if (isset($this->attributes[self::ATTR_PUBLISHED_AT])
            && $this->attributes[self::ATTR_PUBLISHED_AT] === '0000-00-00 00:00:00'
        ) {
            $this->attributes[self::ATTR_PUBLISHED_AT] = null;
        }

        $attributes = $this->attributesToArray();
        $version = $this->getLatestVersion()->toArray();

        $attributes['edited_at'] = $version['created_at'];
        $attributes['edited_by'] = $version['created_by'];

        return array_merge($version, $attributes, $this->relationsToArray());
    }
}
