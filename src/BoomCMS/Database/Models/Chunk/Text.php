<?php

namespace BoomCMS\Database\Models\Chunk;

use BoomCMS\Jobs\TextFilters;
use BoomCMS\Support\Str;
use Illuminate\Support\Facades\Bus;

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

            $siteText = Bus::dispatch(new TextFilters\OEmbed($text));
            $siteText = Bus::dispatch(new TextFilters\StorifyEmbed($siteText));
        }

        $this->attributes['text'] = $text;
        $this->attributes['site_text'] = $siteText;
    }
}
