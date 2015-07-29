<?php

namespace BoomCMS\Database\Models\Chunk\Linkset;

use Illuminate\Database\Eloquent\Model;
use BoomCMS\Core\Link\Link as LinkObject;

class Link extends Model
{
    protected $guarded = ['id'];
    protected $link;
    protected $table = 'chunk_linkset_links';
    public $timestamps = false;

    public function getLink()
    {
        if ($this->link === null) {
            $url = $this->target_page_id > 0 ? $this->target_page_id : $this->url;
            $this->link = LinkObject::factory($url);
        }

        return $this->link;
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }
}
