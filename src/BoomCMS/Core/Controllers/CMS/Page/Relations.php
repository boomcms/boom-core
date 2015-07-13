<?php

namespace BoomCMS\Core\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Controllers\Controller;
use BoomCMS\Core\Page;

use Illuminate\Http\Request;

class Relations extends Controller
{
    /**
     *
     * @var Auth
     */
    public $auth;

    /**
	 *
	 * @var Tag\Provider
	 */
    protected $provider;

    /**
     *
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
}
