<?php

namespace BoomCMS\Listeners;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Foundation\Events\PageEvent;
use BoomCMS\Jobs\CreatePagePrimaryUri;
use Illuminate\Support\Facades\Bus;

class CreatePagePrimaryURL
{
    public function getPrefix(Page $page)
    {
        return ($page->getParent()->getChildPageUrlPrefix()) ?: $page->getParent()->url()->getLocation();
    }

    public function handle(PageEvent $event)
    {
        $page = $event->getPage();

        if ($this->urlShouldBeChanged($event)) {
            Bus::dispatch(new CreatePagePrimaryUri($page, $this->getPrefix($page)));
        }
    }

    public function urlShouldBeChanged(PageEvent $event)
    {
        return $event->getPage()->url() === null;
    }
}
