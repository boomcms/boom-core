<?php

namespace BoomCMS\Database\Models\Chunk;

use BoomCMS\Support\Str;
use Embera\Embera;

class Text extends BaseChunk
{
    protected $table = 'chunk_texts';

    public function setTextAttribute($text)
    {
        $text = str_replace('&nbsp;', ' ', $text);

        if ($this->slotname === 'standfirst') {
            $siteText = $text = strip_tags($text);
        } else {
            $text = Str::makeInternalLinksRelative($text);
            $siteText = Str::StorifyEmbed(Embera::autoEmbed($text));
        }

        $this->attributes['text'] = $text;
        $this->attributes['site_text'] = $siteText;
    }
}
