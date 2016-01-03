<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Page as Page;
use Illuminate\Http\Request;

class Relations extends Controller
{
    /**
     * @var Page\Page;
     */
    protected $related;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->page = $request->route()->getParameter('page');

        $this->authorize('edit', $this->page);

        $this->related = Page::find($this->request->input('related_page_id'));
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
        return view('boomcms::editor.page.settings.relations');
    }
}
