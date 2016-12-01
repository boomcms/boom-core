<?php

namespace BoomCMS\Routing;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\Site as SiteInterface;
use BoomCMS\Database\Models\Page as PageModel;
use BoomCMS\Database\Models\Site as SiteModel;
use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Site;
use BoomCMS\Support\Facades\URL;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Router
{
    /**
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
     * @return SiteInterface
     */
    public function getActiveSite()
    {
        return $this->site;
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function routePage($path)
    {
        $url = URL::findByLocation($path);

        if ($url) {
            $page = $url->getPage();

            if (!$page) {
                // The url is in use but doesn't have a page.
                // The page must have been deleted.
                throw new GoneHttpException();
            }

            if (Editor::isDisabled() && !$page->isVisible()) {
                throw new NotFoundHttpException();
            }

            if (!$page->url()->matches($path)) {
                return redirect((string) $page->url(), 301);
            }

            $this->setActivePage($page);

            return;
        }

        throw new NotFoundHttpException();
    }

    /**
     * @param string $hostname
     *
     * @return $this
     */
    public function routeHostname($hostname)
    {
        $hostname = env('BOOMCMS_HOST', $hostname);

        $site = Site::findByHostname($hostname);
        $site = $site ?: Site::findDefault();

        return $this->setActiveSite($site);
    }

    /**
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setActivePage(PageInterface $page)
    {
        $this->page = $page;
        $this->app->instance(PageModel::class, $page);

        return $this;
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
