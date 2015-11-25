<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Mdoels\Person as PersonInterface;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class AssetVersion extends Model
{
    const ATTR_ID = 'id';
    const ATTR_ASSET = 'asset_id';
    const ATTR_WIDTH = 'width';
    const ATTR_HEIGHT = 'height';
    const ATTR_FILENAME = 'filename';
    const ATTR_FILESIZE = 'filesize';
    const ATTR_EDITED_AT = 'edited_at';
    const ATTR_EDITED_BY = 'edited_by';
    const ATTR_EXTENSION = 'extension';
    const ATTR_MIME = 'mimetype';

    public $table = 'asset_versions';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    /**
     * @var PersonInterface
     */
    protected $editedBy;

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
            $this->editedBy = $this->hasOne(Person::class, static::ATTR_EDITED_BY);
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
     * @return int
     */
    public function getId()
    {
        return (int) $this->{self::ATTR_ID};
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
