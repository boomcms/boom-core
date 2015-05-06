<?php

namespace BoomCMS\Core\Http\Middleware;

use Closure;
use BoomCMS\Core\Page\Provider;
use BoomCMS\Core\Editor\Editor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

class ProcessSiteURL
{
    /**
     *
     * @var Provider
     */
    protected $pageProvider;

    public function __construct(Provider $pageProvider, Editor $editor)
    {
        $this->pageProvider = $pageProvider;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // TODO: 2015/04/29: findByUri won't find a deleted page. Need to make it an option.
        $page = $this->pageProvider->findByUri($request->path());

         if ( ! $page->loaded()) {
             throw new NotFoundHttpException;
         }

        if ($page->isDeleted()) {
            throw new GoneHttpException;
        }

        if ($this->editor->isDisabled() && ! $page->isVisible()) {
            throw new NotFoundHttpException;
        }

        if ($page->url()->location !== $request->path) {
            redirect($page->url(), 301);
        }

        $request->route()->setParameter('boom.currentPage', $page);

        return $next($request);
    }
}