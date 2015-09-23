<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\SearchText;
use BoomCMS\Foundation\Events\PageVersionEvent;

class RemoveExpiredSearchTexts
{
    public function handle(PageVersionEvent $event)
    {
        $version = $event->getVersion();

        SearchText::where('page_id', '=', $version->getPageId())
            ->where('embargoed_until', '<', $version->getEmbargoedUntil()->getTimestamp())
            ->delete();
    }
}
