<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Page as Page;
use BoomCMS\Core\Page\Command\Delete as Delete;

class Page extends CMS
{
    protected $viewPrefix = 'boom::editor.page.';

    /**
	*
	* @var \Boom\Page
	*/
    public $page;
    
    public function __construct(Page\Provider $provider)
    {
        $this->provider = $provider;
        $this->page = $this->provider->findById($this->request->param('id'));
    }

    public function add()
    {
        $this->authorization('add_page', $this->page);

        $creator = new \Boom\Page\Creator($this->page, $this->person);
        $creator->setTemplateId($this->request->input('template_id'));
        $creator->setTitle($this->request->input('title'));
        $new_page = $creator->execute();

        // Add a default URL.
        // This needs to go into a class of some sort, not sure where though.
        // Don't want it as part of Page_Creator because we sometimes want to create the default URLs in this format.
        $prefix = ($this->page->getChildPageUrlPrefix()) ? $this->page->getChildPageUrlPrefix() : $this->page->url()->location;
        $url = \Boom\Page\URL::fromTitle($prefix, $new_page->getTitle());
        \Boom\Page\URL::createPrimary($url, $new_page->getId());

        $this->log("Added a new page under " . $this->page->getTitle(), "Page ID: " . $new_page->getId());

        $new_page->getTemplate()->onPageCreate($new_page);

        $this->response->body(json_encode([
            'url' => URL::site($url, $this->request),
            'id' => $new_page->getId(),
        ]));
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

            $this->page->getTemplate()->onPageDelete($this->page);

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
