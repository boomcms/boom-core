<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Commands\CreatePage;
use BoomCMS\Commands\CreatePagePrimaryUri;
use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page as Page;
use BoomCMS\Events\PageWasCreated;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

class PageController extends Controller
{
    use DispatchesCommands;

    protected $viewPrefix = 'boom::editor.page.';

    /**
     * @var Page\Page
     */
    protected $page;

    public function __construct(Page\Provider $provider, Auth $auth, Request $request)
    {
        $this->provider = $provider;
        $this->auth = $auth;
        $this->request = $request;
        $this->page = $this->request->route()->getParameter('page');
    }

    public function add()
    {
        $this->authorization('add_page', $this->page);

        $newPage = $this->dispatch(new CreatePage($this->provider, $this->auth, $this->page));

        $urlPrefix = ($this->page->getChildPageUrlPrefix()) ?: $this->page->url()->getLocation();
        $url = $this->dispatch(new CreatePagePrimaryUri($newPage, $urlPrefix));

        Event::fire(new PageWasCreated($newPage, $this->page));

        return [
            'url' => (string) $url,
            'id'  => $newPage->getId(),
        ];
    }

    public function discard()
    {
        $this->page->deleteDrafts();
    }

    public function urls()
    {
        return View::make($this->viewPrefix.'urls', [
            'page' => $this->page,
            'urls' => $this->page->getUrls(),
        ]);
    }
}
