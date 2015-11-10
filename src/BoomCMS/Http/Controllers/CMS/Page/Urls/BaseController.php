<?php

namespace BoomCMS\Http\Controllers\CMS\Page\Urls;

use BoomCMS\Core\URL\URL;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Facades\URL as URLFacade;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::editor.urls';

    public $url;

    /**
     * @var Page\Page
     */
    public $page;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->page = Page::findById($request->input('page_id'));

        if ($id = $request->route()->getParameter('id')) {
            $this->url = URLFacade::findById($id);
        } else {
            $this->url = new URL([]);
        }

        if ($request->route()->getParameter('id') && !$this->url->loaded()) {
            about(404);
        }

        if ($request->input('page_id') && !$this->page->loaded()) {
            abourt(404);
        }

        if ($this->page->loaded()) {
            parent::authorization('edit_page_urls', $this->page);
        }
    }
}
