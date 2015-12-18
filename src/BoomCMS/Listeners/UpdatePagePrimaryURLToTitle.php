<?php

namespace BoomCMS\Listeners;

use BoomCMS\Events\PageTitleWasChanged;

class UpdatePagePrimaryURLToTitle extends CreatePagePrimaryURL
{
    public function urlShouldBeChanged(PageTitleWasChanged $event)
    {
        $oldTitle = $event->getOldTitle();

        return ($oldTitle !== $event->getNewTitle())
            && $oldTitle == 'Untitled'
            && $event->getPage()->url()->getLocation() !== '/';
    }
}
