<?php

namespace BoomCMS\Database\Models\Asset;

use BoomCMS\Core\Asset\Asset;
use BoomCMS\Support\Facades\Person;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    public $table = 'asset_versions';
    public $guarded = ['id'];
    public $timestamps = false;

    public function getEditedAt()
    {
        return (new DateTime())->setTimestamp($this->attributes['edited_at']);
    }

    public function getEditedBy()
    {
        return Person::find($this->attributes['edited_by']);
    }

    public function getId()
    {
        return $this->attributes['id'];
    }

    public function scopeForAsset($query, Asset $asset)
    {
        return $query
            ->where('asset_id', '=', $asset->getId())
            ->orderBy('edited_at', 'desc');
    }

    public function setExtensionAttribute($value)
    {
        $extension = preg_replace('|[^a-z0-9]|', '', strtolower($value));

        $this->attributes['extension'] = $extension;
    }
}
