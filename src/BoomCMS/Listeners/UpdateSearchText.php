<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\SearchText;
use BoomCMS\Events\PageSearchSettingsWereUpdated;
use BoomCMS\Support\Facades\Chunk;

class UpdateSearchText
{
    public function handle(PageSearchSettingsWereUpdated $event)
    {
        $page = $event->getPage();
        $version = $page->getCurrentVersion();

        $standfirst = Chunk::find('text', 'standfirst', $version);
        $description = $page->getDescription();
        $description = ($description == $standfirst) ? '' : $description;

        SearchText::where('page_vid', '=', $version->getId())
            ->update([
                'meta' => $page->getKeywords().' '.$description
            ]);
    }
}
