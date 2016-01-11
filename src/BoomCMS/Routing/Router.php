<?php

namespace BoomCMS\Routing;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Site as SiteModel;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\Site;
use BoomCMS\Support\Facades\URL;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router
{
    /**
     *
     * @var Application
     */
    protected $app;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * @var SiteInterface
     */
    protected $site;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return PageInterface
     */
    public function getActivePage()
    {
        return $this->page;
    }

    /**
     * 
     * @return SiteInterface
     */
    public function getActiveSite()
    {
        return $this->site;
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
            if (!$url || !$url->getPage()->isVisible()) {
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

    /**
     * @param string $hostname
     */
    public function routeHostname($hostname)
    {
        $hostname = env('BOOMCMS_HOST', $hostname);

        $site = Site::findByHostname($hostname);
        $site = $site ?: Site::findDefault();
        
        $this->setActiveSite($site);
    }

    /**
     * @param SiteInterface $site
     *
     * @return $this
     */
    public function setActiveSite(SiteInterface $site)
    {
        $this->site = $site;
        $this->app->instance(SiteModel::class, $site);

        return $this;
    }
}
