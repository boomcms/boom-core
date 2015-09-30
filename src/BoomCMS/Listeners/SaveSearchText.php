<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\SearchText;
use BoomCMS\Foundation\Events\PageVersionEvent;
use BoomCMS\Support\Facades\Chunk;

class SaveSearchText
{
    public function handle(PageVersionEvent $event)
    {
        $page = $event->getPage();
        $version = $event->getVersion();

        $standfirst = Chunk::find('text', 'standfirst', $version);
        $bodycopy = Chunk::find('text', 'bodycopy', $version);
        $description = $page->getDescription();
        $description = ($description == $standfirst) ? '' : $description;

        SearchText::create([
            'page_id'         => $page->getId(),
            'page_vid'        => $version->getId(),
            'embargoed_until' => $version->getEmbargoedUntil()->getTimestamp(),
            'title'           => $version->getTitle(),
            'standfirst'      => $standfirst ? $standfirst->text : '',
            'text'            => $bodycopy ? strip_tags($bodycopy->text) : '',
            'meta'            => $page->getKeywords().' '.$description,
        ]);
    }
}
