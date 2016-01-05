<?php

namespace BoomCMS\Routing;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;

class Router
{
    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var string
     */
    protected $requestUri;

    /**
     * @return PageInterface
     */
    public function getActivePage()
    {
        return $this->page;
    }

    /**
     * @param string $uri
     * @return mixed
     */
    public function process($uri)
    {
        $this->page = Page::findByUri($uri);

        if (!$this->page) {
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

        if (!$this->page->url()->is($uri)) {
            return redirect((string) $page->url(), 301);
        }
    }
}
