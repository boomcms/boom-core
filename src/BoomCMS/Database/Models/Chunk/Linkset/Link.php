<?php

namespace BoomCMS\Database\Models\Chunk\Linkset;

use BoomCMS\Foundation\Database\Model;
use BoomCMS\Link\Link as LinkObject;

class Link extends Model
{
    const ATTR_TITLE = 'title';
    const ATTR_TEXT = 'text';

    protected $link;
    protected $table = 'chunk_linkset_links';

    public function getLink()
    {
        if ($this->link === null) {
            $url = $this->target_page_id > 0 ? $this->target_page_id : $this->url;
            $this->link = LinkObject::factory($url);
        }

        return $this->link;
    }

    /**
     * @param string $value
     */
    public function setTitleAttribute($value)
    {
        $this->attributes[self::ATTR_TITLE] = trim(strip_tags($value));
    }

    /**
     * @param string $value
     */
    public function setTextAttribute($value)
    {
        $this->attributes[self::ATTR_TEXT] = trim(strip_tags($value));
    }
}
