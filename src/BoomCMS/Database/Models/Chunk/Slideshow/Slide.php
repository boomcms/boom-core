<?php

namespace BoomCMS\Database\Models\Chunk\Slideshow;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Foundation\Database\Model;
use BoomCMS\Support\Helpers\URL;

class Slide extends Model
{
    const ATTR_CAPTION = 'caption';
    const ATTR_LINK_TEXT = 'link_text';
    const ATTR_URL = 'url';

    protected $table = 'chunk_slideshow_slides';

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
