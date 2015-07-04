<?php

namespace BoomCMS\Core\Models\Chunk;

class Location extends BaseChunk
{
    protected $table = 'chunk_locations';

    public function setAddressAttribute($value)
    {
        $this->attributes['address'] = nl2br(trim(strip_tags($value)));
    }

    public function setPostcodeAttribute($value)
    {
        $this->attributes['postcode'] = trim(strip_tags($value));
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }
}
