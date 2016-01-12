<?php

namespace BoomCMS\Routing;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     *
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
            if (!$url || ($url->getPage() && !$url->getPage()->isVisible())) {
                throw new NotFoundHttpException();
            }

            // The url is in use but doesn't have a page.
            // The page must have been deleted.
            //
            // 410.
            throw new GoneHttpException();
        }

        if (Editor::isDisabled() && !$this->page->isVisible()) {
            throw new NotFoundHttpException();
        }

        if (!$this->page->url()->is($uri)) {
            return redirect((string) $this->page->url(), 301);
        }
    }
}
