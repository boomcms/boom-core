<?php

namespace BoomCMS\Routing;

use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;
use Illuminate\Http\Request;

class Router
{
    public function process(Request $request)
    {
        $uri = $request->route()->getParameter('location');
        $page = Page::findByUri($uri);

        if (!$page) {
            $url = URL::findByLocation($uri);

            // The URL isn't in use or
            // The URL is in use and has a page - the page must not be visible to the current user
            //
            // 404.
            if (!$url || !$url->getPage()->isVisible()) {
                abort(404);
            }

            // The url is in use but doesn't have a page.
            // The page must have been deleted.
            //
            // 410.
            abort(410);
        }

        if (Editor::isDisabled() && !$page->isVisible()) {
            abort(404);
        }

        if (!$page->url()->is($uri)) {
            return redirect((string) $page->url(), 301);
        }
    }
}
