<?php

namespace BoomCMS\Database\Models\Chunk\Slideshow;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Helpers\URL;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    const ATTR_ID = 'id';
    const ATTR_CAPTION = 'caption';
    const ATTR_LINK_TEXT = 'link_text';
    const ATTR_URL = 'url';

    protected $table = 'chunk_slideshow_slides';
    protected $guarded = [self::ATTR_ID];
    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function setCaptionAttribute($value)
    {
        $this->attributes[self::ATTR_CAPTION] = strip_tags($value);
    }

    public function setLinkTextAttribute($value)
    {
        $this->attributes[self::ATTR_LINK_TEXT] = strip_tags($value);
    }

    public function setUrlAttribute($value)
    {
        $this->attributes[self::ATTR_URL] = URL::makeRelative($value);
    }
}
