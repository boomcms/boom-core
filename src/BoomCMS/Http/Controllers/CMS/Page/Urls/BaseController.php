<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Urls;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page;
use BoomCMS\Core\URL;
use BoomCMS\Http\Controllers\Controller;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
	 *
	 * @var string
	 */
    protected $viewPrefix = "boom::editor.urls";

    public $url;

    /**
	 *
	 * @var Page\Page
	 */
    public $page;

    /**
     *
     * @var Page\Provider
     */
    public $pageProvider;

    /**
     *
     * @var URL\Provider
     */
    public $provider;

    public function __construct(Auth $auth,
        Request $request,
        Page\Provider $pageProvider,
        URL\Provider $provider
    )
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->pageProvider = $pageProvider;
        $this->provider = $provider;
        $this->page = $pageProvider->findById($request->input('page_id'));

        if ($id = $request->route()->getParameter('id')) {
            $this->url = $this->provider->findById($id);
        } else {
            $this->url = new URL\URL([]);
        }

        if ($request->route()->getParameter('id') && ! $this->url->loaded()) {
            about(404);
        }

        if ($request->input('page_id') && ! $this->page->loaded()) {
            abourt(404);
        }

        if ($this->page->loaded()) {
            parent::authorization('edit_page_urls', $this->page);
        }
    }
}
