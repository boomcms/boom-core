<?php

namespace BoomCMS\Http\Middleware;

use BoomCMS\Support\Facades\Editor;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProcessSiteURL
{
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
        $page = Page::findByUri($uri);

        if (!$page->loaded()) {
            $url = URL::findByLocation($uri);

            // The URL isn't in use or
            // The URL is in use and has a page - the page must not be visible to the current user
            //
            // 404.
            if (!$url || ($url && $url->getPage()->loaded())) {
                throw new NotFoundHttpException();
            }

            // The url is in use but doesn't have a page.
            // The page must have been deleted.
            //
            // 410.
            throw new GoneHttpException();
        }

        if (Editor::isDisabled() && !$page->isVisible()) {
            throw new NotFoundHttpException();
        }

        if (!$page->url()->is($uri)) {
            return redirect((string) $page->url(), 301);
        }

        $request->route()->setParameter('page', $page);
        Editor::setActivePage($page);

        View::share('page', $page);

        return $next($request);
    }
}
