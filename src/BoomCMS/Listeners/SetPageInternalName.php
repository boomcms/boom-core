<?php

namespace BoomCMS\Listeners;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\PageTitleWasChanged;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Str;

class SetPageInternalName extends CreatePagePrimaryURL
{
    public function handle(PageTitleWasChanged $event)
    {
        $page = $event->getPage();
        $title = $event->getNewTitle();

        if ($title !== Page::DEFAULT_TITLE && empty($page->getInternalName())) {
            $slug = Str::slug($title);

            $unique = Str::unique($slug, function ($name) {
                return PageFacade::internalNameExists($name) === false;
            });

            $page->setInternalName($unique);

            PageFacade::save($page);
        }
    }
}
