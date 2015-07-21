<?php

namespace BoomCMS\Core\Models\Chunk\Slideshow;

use Illuminate\Database\Eloquent\Model;
use BoomCMS\Core\URL\Helpers as URL;

class Slide extends Model
{
    protected $table = 'chunk_slideshow_slides';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function asset()
    {
        return $this->belongsTo('BoomCMS\Core\Models\Asset');
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
