<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Page\Provider;
use BoomCMS\Core\Editor\Editor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        // TODO: 2015/04/29: findByUri won't find a deleted page. Need to make it an option.
        $page = $this->pageProvider->findByUri($request->route()->getParameter('location'));

         if ( !$page->loaded()) {
             throw new NotFoundHttpException();
         }

        if ($page->isDeleted()) {
            throw new GoneHttpException();
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
