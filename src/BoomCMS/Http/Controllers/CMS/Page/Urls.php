<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Jobs\MakeURLPrimary;
use BoomCMS\Jobs\ReassignURL;
use BoomCMS\Support\Facades\URL;
use Illuminate\Support\Facades\Bus;
use Illuminate\Http\Request;

class Urls extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::editor.urls';

    public $url;

    /**
     * @var PageInterface
     */
    public $page;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->page = $request->route()->getParameter('page');
        $this->url = $request->route()->getParameter('url');

        $this->authorization('edit_page_urls', $this->page);
    }

    public function getAdd()
    {
        return view("$this->viewPrefix.add", [
            'page' => $this->page,
        ]);
    }

    public function getMove()
    {
        return view("$this->viewPrefix.move", [
            'url'     => $this->url,
            'current' => $this->url->getPage(),
            'page'    => $this->page,
        ]);
    }

    public function postAdd()
    {
        $location = $this->request->input('location');
        $this->url = URL::findByLocation($location);

        if ($this->url && !$this->url->isForPage($this->page)) {
            // Url is being used for a different page.
            // Notify that the url is already in use so that the JS can load a prompt to move the url.
            return ['existing_url_id' => $this->url->getId()];
        } elseif (!$this->url) {
            URL::create($location, $this->page->getId());
        }
    }

    public function postDelete()
    {
        if (!$this->url->isPrimary()) {
            URL::delete($this->url);
        }
    }

    public function postMakePrimary()
    {
        Bus::dispatch(new MakeURLPrimary($this->url));
    }

    public function postMove()
    {
        Bus::dispatch(new ReassignURL($this->url, $this->page));
    }
}
