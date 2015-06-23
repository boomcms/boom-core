<?php

namespace BoomCMS\Core\Models\Chunk;

use BoomCMS\Core\Commands\TextFilters;
use Illuminate\Support\Facades\Bus;

class Text extends BaseChunk
{
    protected $table = 'chunk_texts';

    public function setTextAttribute($text)
    {
        $text = str_replace('&nbsp;', ' ', $text);

        if ($this->slotname === 'standfirst') {
            $siteText = $text = Bus::dispatch(new TextFilters\RemoveAllHTML($text));
        } else {
            $text = Bus::dispatch(new TextFilters\MakeInternalLinksRelative($text));

            $siteText = Bus::dispatch(new TextFilters\OEmbed($text));
            $siteText = Bus::dispatch(new TextFilters\StorifyEmbed($siteText));
        }

        $this->attributes['text'] = $text;
        $this->attributes['site_text'] = $siteText;
    }
}
