<?php

namespace BoomCMS\Database\Models\Chunk\Slideshow;

use BoomCMS\Database\Models\Asset;
use BoomCMS\Support\Helpers\URL;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $table = 'chunk_slideshow_slides';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function setCaptionAttribute($value)
    {
        $this->attributes['caption'] = strip_tags($value);
    }

    public function setLinkTextAttribute($value)
    {
        $this->attributes['link_text'] = strip_tags($value);
    }

    public function setUrlAttribute($value)
    {
        $this->attributes['url'] = URL::makeRelative($value);
    }
}
