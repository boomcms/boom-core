<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Page;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Page as PageFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class Relations extends Controller
{
    /**
     * @var Auth
     */
    public $auth;

    /**
     * @var Tag\Provider
     */
    protected $provider;

    /**
     * @var Page\Page;
     */
    protected $related;

    public function __construct(Auth $auth, Request $request, Page\Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->page = $request->route()->getParameter('page');
        $this->provider = $provider;

        $this->authorization('edit_page', $this->page);

        $this->related = $this->provider->findById($this->request->input('related_page_id'));
    }

    public function add()
    {
        $this->page->addRelation($this->related);
    }

    public function remove()
    {
        $this->page->removeRelation($this->related);
    }

    public function view()
    {
        return View::make('boom::editor.page.settings.relations', [
            'relatedPages' => PageFacade::findRelatedTo($this->page),
        ]);
    }
}
