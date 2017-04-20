<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\Page;
use BoomCMS\Foundation\Events\PageEvent;

class UpdatePagePrimaryURLToTitle extends CreatePagePrimaryURL
{
    public function urlShouldBeChanged(PageEvent $event)
    {
        $oldTitle = $event->getOldTitle();

        return ($oldTitle !== $event->getNewTitle())
            && $oldTitle === Page::DEFAULT_TITLE
            && $event->getPage()->url()->getLocation() !== '/';
    }
}
