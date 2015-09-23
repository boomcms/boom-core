<?php

namespace BoomCMS\Listeners;

use BoomCMS\Foundation\Events\PageEvent;
use BoomCMS\Database\Models\SearchText;

class RemovePageFromSearch
{
    public function handle(PageEvent $event)
    {
        $page = $event->getPage();

        SearchText::where('page_id', '=', $page->getId())->delete();
    }
}
