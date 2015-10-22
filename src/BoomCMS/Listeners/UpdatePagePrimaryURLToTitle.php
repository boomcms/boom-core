<?php

namespace BoomCMS\Listeners;

use BoomCMS\Jobs\CreatePagePrimaryUri;
use BoomCMS\Core\Page\Page;
use BoomCMS\Events\PageTitleWasChanged;
use Illuminate\Support\Facades\Bus;

class UpdatePagePrimaryURLToTitle
{
    public function getPrefix(Page $page)
    {
        return ($page->getParent()->getChildPageUrlPrefix()) ?: $page->getParent()->url()->getLocation();
    }

    public function handle(PageTitleWasChanged $event)
    {
        if ($this->urlShouldBeChanged($event)) {
            $page = $event->getPage();

            Bus::dispatch(new CreatePagePrimaryUri($page, $this->getPrefix($page)));
        }
    }

    public function urlShouldBeChanged(PageTitleWasChanged $event)
    {
        $oldTitle = $event->getOldTitle();

        return ($oldTitle !== $event->getNewTitle())
            && $oldTitle == 'Untitled'
            && $event->getPage()->url()->getLocation() !== '/';
    }
}
