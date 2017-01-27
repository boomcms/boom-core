<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Mdoels\Person as PersonInterface;
use BoomCMS\Foundation\Database\Model;
use DateTime;

class AssetVersion extends Model
{
    const ATTR_ASSET = 'asset_id';
    const ATTR_WIDTH = 'width';
    const ATTR_HEIGHT = 'height';
    const ATTR_FILENAME = 'filename';
    const ATTR_FILESIZE = 'filesize';
    const ATTR_CREATED_AT = 'created_at';
    const ATTR_CREATED_BY = 'created_by';
    const ATTR_EXTENSION = 'extension';
    const ATTR_MIME = 'mimetype';
    const ATTR_METADATA = 'metadata';

    protected $casts = [
        self::ATTR_ID       => 'integer',
        self::ATTR_ASSET    => 'integer',
        self::ATTR_METADATA => 'array',
    ];

    public $table = 'asset_versions';

    /**
     * @var PersonInterface
     */
    protected $editedBy;

    public function editedBy()
    {
        return $this->belongsTo(Person::class, static::ATTR_CREATED_BY);
    }

    public function getAsset()
    {
        return $this->belongsTo(Asset::class, self::ATTR_ASSET, Asset::ATTR_ID)->first();
    }

    /**
     * @return int
     */
    public function getAssetId()
    {
        return $this->{self::ATTR_ASSET};
    }

    public function getEditedAt()
    {
        return (new DateTime())->setTimestamp($this->attributes[self::ATTR_CREATED_AT]);
    }

    /**
     * @return PersonInterface
     */
    public function getEditedBy()
    {
        if ($this->editedBy === null) {
            $this->editedBy = $this->editedBy()->first();
        }

        return $this->editedBy;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->{self::ATTR_EXTENSION};
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->{self::ATTR_FILENAME};
    }

    /**
     * @return int
     */
    public function getFilesize()
    {
        return (int) $this->{self::ATTR_FILESIZE};
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->{self::ATTR_HEIGHT};
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        $data = $this->{self::ATTR_METADATA};

        return $data ? (array) $data : [];
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->{self::ATTR_MIME};
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->{self::ATTR_WIDTH};
    }

    public function setExtensionAttribute($value)
    {
        $extension = preg_replace('|[^a-z0-9]|', '', strtolower($value));

        $this->attributes[self::ATTR_EXTENSION] = $extension;
    }

    public function setFilenameAttribute($value)
    {
        $this->attributes[self::ATTR_FILENAME] = str_replace(['/', '\\'], '', $value);
    }
}
