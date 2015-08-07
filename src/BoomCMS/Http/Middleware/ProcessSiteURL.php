<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Core\Page\Provider;
use BoomCMS\Core\URL\Provider as URLProvider;
use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProcessSiteURL
{
    /**
     * @var Provider
     */
    protected $pageProvider;

    /**
     * @var URLProvider
     */
    protected $urlProvider;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Provider $pageProvider,
        URLProvider $urlProvider,
        Application $application
    ) {
        $this->pageProvider = $pageProvider;
        $this->urlProvider = $urlProvider;
        $this->app = $application;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->route()->getParameter('location');
        $page = $this->pageProvider->findByUri($uri);

        if (!$page->loaded()) {
            $url = $this->urlProvider->findByLocation($uri);

            // The URL isn't in use or
            // The URL is in use and has a page - the page must not be visible to the current user
            //
            // 404.
            if (!$url->loaded() || ($url->loaded() && $url->getPage()->loaded())) {
                throw new NotFoundHttpException();
            }

            // The url is in use but doesn't have a page.
            // The page must have been deleted.
            //
            // 410.
            throw new GoneHttpException();
        }

        if ($this->app['boomcms.editor']->isDisabled() && !$page->isVisible()) {
            throw new NotFoundHttpException();
        }

        if (!$page->url()->is($uri)) {
            return redirect((string) $page->url(), 301);
        }

        $request->route()->setParameter('page', $page);
        $this->app['boomcms.editor']->setActivePage($page);

        View::share('page', $page);

        return $next($request);
    }
}
