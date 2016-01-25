<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\View\View;

class Relations extends Controller
{
    /**
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->authorize('edit', $page);
    }

    /**
     * @param Page $page
     * @param Page $related
     */
    public function destroy(Page $page, Page $related)
    {
        $page->removeRelation($related);
    }

    /**
     * @return View
     */
    public function index()
    {
        return view('boomcms::editor.page.settings.relations');
    }

    /**
     * @param Page $page
     * @param Page $related
     */
    public function store(Page $page, Page $related)
    {
        $page->addRelation($related);
    }
}
