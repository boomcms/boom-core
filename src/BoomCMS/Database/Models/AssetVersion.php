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
    const ATTR_EDITED_AT = 'edited_at';
    const ATTR_EDITED_BY = 'edited_by';
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
        return (new DateTime())->setTimestamp($this->attributes[self::ATTR_EDITED_AT]);
    }

    /**
     * @return PersonInterface
     */
    public function getEditedBy()
    {
        if ($this->editedBy === null) {
            $this->editedBy = $this->belongsTo(Person::class, static::ATTR_EDITED_BY)->first();
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
}
