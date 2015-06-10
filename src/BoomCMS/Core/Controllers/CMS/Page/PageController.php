<?php

namespace BoomCMS\Core\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page as Page;
use BoomCMS\Core\Page\Command\Delete as Delete;
use BoomCMS\Core\Controllers\Controller as Controller;
use BoomCMS\Core\URL\Helpers as URLHelper;

use Illuminate\Http\Request;

class PageController extends Controller
{
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

        $newPage = $this->dispatch('BoomCMS\Core\Commands\CreatePage', [$this->provider, $this->auth, $parent]);

        $urlPrefix = ($this->page->getChildPageUrlPrefix()) ?: $this->page->url()->getLocation();
        $url = $this->dispatch('BoomCMS\Core\Commands\CreatePagePrimaryURI', [$this->provider, $newPage, $urlPrefix]);

        return [
            'url' => (string) $newPage->url(),
            'id' => $newPage->getId(),
        ];
    }

    public function delete()
    {
        if ( ! ($this->page->wasCreatedBy($this->person) || $this->auth->loggedIn('delete_page', $this->page) || $this->auth->loggedIn('manage_pages')) || $this->page->isRoot()) {
            throw new HTTP_Exception_403();
        }

        if ($this->request->method() === Request::GET) {
            $finder = new \Boom\Page\Finder();
            $finder->addFilter(new \Boom\Page\Finder\Filter\ParentId($this->page->getId()));
            $children = $finder->count();

            // Get request
            // Show a confirmation dialogue warning that child pages will become inaccessible and asking whether to delete the children.
            return View::make($this->viewPrefix . 'delete', [
                'count' => $children,
                'page' =>$this->page,
            ]);
        } else {
            $this->log("Deleted page " . $this->page->getTitle() . " (ID: " . $this->page->getId() . ")");

            // Redirect to the parent page after we've finished.
            $this->response->body($this->page->parent()->url());

            $commander = new \Boom\Page\Commander($this->page);
            $commander
                ->addCommand(new Delete\FromFeatureBoxes())
                ->addCommand(new Delete\FromLinksets());

            ($this->request->input('with_children') == 1) && $commander->addCommand(new Delete\Children());

            $commander->addCommand(new Delete\FlagDeleted());
            $commander->execute();
        }
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
