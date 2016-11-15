<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\URL;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Jobs\MakeURLPrimary;
use BoomCMS\Jobs\ReassignURL;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Http\Request;

class Urls extends Controller
{
    protected $viewPrefix = 'boomcms::editor.urls';

    /**
     * @param Page $page
     */
    protected function auth(Page $page)
    {
        $this->authorize('editUrls', $page);
    }

    public function index(Page $page)
    {
        $this->auth($page);

        return view($this->viewPrefix.'.list', [
            'page' => $page,
            'urls' => $page->getUrls(),
        ]);
    }

    public function getMove(Page $page, URL $url)
    {
        $this->auth($page);

        return view("$this->viewPrefix.move", [
            'url'     => $url,
            'current' => $url->getPage(),
            'page'    => $page,
        ]);
    }

    public function store(Request $request, Page $page)
    {
        $this->auth($page);

        $location = $request->input('location');
        $url = URLFacade::findByLocation($location);

        if ($url && !$url->isForPage($page)) {
            // Url is being used for a different page.
            // Notify that the url is already in use so that the JS can load a prompt to move the url.

            return ['existing_url_id' => $url->getId()];
        }

        if (!$url) {
            URLFacade::create($location, $page);
        }
    }

    public function destroy(Page $page, URL $url)
    {
        $this->auth($page);

        if (!$url->isPrimary()) {
            URLFacade::delete($url);
        }
    }

    public function makePrimary(Page $page, URL $url)
    {
        $this->auth($page);

        $this->dispatch(new MakeURLPrimary($url));
    }

    public function postMove(Page $page, URL $url)
    {
        $this->auth($page);

        $this->dispatch(new ReassignURL($url, $page));
    }
}
