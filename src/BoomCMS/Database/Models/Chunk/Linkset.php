<?php

namespace BoomCMS\Database\Models\Chunk;

use BoomCMS\Support\Helpers\URL;

class Linkset extends BaseChunk
{
    const ATTR_LINKS = 'links';
    const ATTR_TITLE = 'title';

    protected $table = 'chunk_linksets';

    protected $casts = [
        self::ATTR_LINKS => 'json',
    ];

    public function setLinksAttribute($links)
    {
        foreach ($links as &$link) {
            if (isset($link['title'])) {
                $link['title'] = trim(strip_tags($link['title']));
            }

            if (isset($link['text'])) {
                $link['text'] = trim(strip_tags($link['text']));
            }

            if (isset($link['url'])) {
                $link['url'] = URL::makeRelative($link['url']);
            }
        }

        $this->attributes[self::ATTR_LINKS] = json_encode($links);
    }

    public function setTitleAttribute($value)
    {
        $this->attributes[self::ATTR_TITLE] = trim(strip_tags($value));
    }
}
