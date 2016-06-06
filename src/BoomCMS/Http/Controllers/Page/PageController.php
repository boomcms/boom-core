<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Events\PageWasCreated;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Jobs\CreatePage;
use BoomCMS\Support\Facades\PageVersion;
use BoomCMS\Support\Facades\URL;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class PageController extends Controller
{
    public function getIndex(Request $request)
    {
        return Helpers::getPages($request->input());
    }

    public function postAdd(Site $site, Page $page)
    {
        $this->authorize('add', $page);

        $parent = $page->getAddPageParent();
        $newPage = $this->dispatch(new CreatePage(auth()->user(), $site, $parent));

        Event::fire(new PageWasCreated($newPage, $page));

        $url = URL::page($newPage);

        return [
            'url' => (string) $url,
            'id'  => $newPage->getId(),
        ];
    }

    public function postDiscard(Page $page)
    {
        $this->authorize('edit', $page);

        PageVersion::deleteDrafts($page);
    }
}
