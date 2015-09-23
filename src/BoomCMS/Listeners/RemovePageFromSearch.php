<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\SearchText;
use BoomCMS\Foundation\Events\PageEvent;

class RemovePageFromSearch
{
    public function handle(PageEvent $event)
    {
        $page = $event->getPage();

        SearchText::where('page_id', '=', $page->getId())->delete();
    }
}
