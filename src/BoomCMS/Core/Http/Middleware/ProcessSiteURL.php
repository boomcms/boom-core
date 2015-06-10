<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Page\Provider;
use BoomCMS\Core\Editor\Editor;
use BoomCMS\Core\URL\Helpers as BoomURL;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ProcessSiteURL
{
    /**
     *
     * @var Provider
     */
    protected $pageProvider;

    /**
     *
     * @var Editor
     */
    protected $editor;

    public function __construct(Provider $pageProvider, Editor $editor)
    {
        $this->pageProvider = $pageProvider;
        $this->editor = $editor;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->route()->getParameter('location');
        $page = $this->pageProvider->findByUri($uri);

         if ( !$page->loaded()) {
             // Check whether the URL exists in the DB.
             // If so the page has been deleted.
             // If not the page never existed.
             // TODO: this isn't quite working as expected - if the user isn't logged in and the page is invisible they'll get a gone expection rather than 404.
             if ( !BoomURL::isAvailable($uri)) {
                 throw new GoneHttpException();
             }
             
             throw new NotFoundHttpException();
         }

        if ($this->editor->isDisabled() && ! $page->isVisible()) {
            throw new NotFoundHttpException();
        }

        if ( !$page->url()->is($request->route()->getParameter('location'))) {
            redirect($page->url(), 301);
        }

        $request->route()->setParameter('boomcms.currentPage', $page);

        View::share('page', $page);

        return $next($request);
    }
}
