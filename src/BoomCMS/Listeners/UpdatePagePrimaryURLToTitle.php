<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\PageTitleWasChanged;

class UpdatePagePrimaryURLToTitle extends CreatePagePrimaryURL
{
    public function urlShouldBeChanged(PageTitleWasChanged $event)
    {
        $oldTitle = $event->getOldTitle();

        return ($oldTitle !== $event->getNewTitle())
            && $oldTitle === Page::DEFAULT_TITLE
            && $event->getPage()->url()->getLocation() !== '/';
    }
}
