<?php

namespace BoomCMS\Core\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page as Page;

use BoomCMS\Core\Commands\CreatePage;
use BoomCMS\Core\Commands\CreatePagePrimaryUri;
use BoomCMS\Core\Controllers\Controller;

use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesCommands;

class PageController extends Controller
{
    use DispatchesCommands;

    protected $viewPrefix = 'boom::editor.page.';

    /**
     *
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
        $url = $this->dispatch(new CreatePagePrimaryUri($this->provider, $newPage, $urlPrefix));

        return [
            'url' => (string) $url,
            'id' => $newPage->getId(),
        ];
    }

    public function discard()
    {
        $commander = new Page\Commander($this->page);
        $commander->addCommand(new Page\Command\Delete\Drafts());
        $commander->execute();
    }

    /**
	 * Reverts the current page to the last published version.
	 *
	 * @uses	Model_Page::stash()
	 */
    public function stash()
    {
        $this->page->stash();
    }

    public function urls()
    {
        return View::make($this->viewPrefix . 'urls', [
            'page' => $this->page,
            'urls' => $this->page->getUrls(),
        ]);
    }
}
