<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\PageRelationshipAdded;
use BoomCMS\Events\PageRelationshipRemoved;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class Relations extends Controller
{
    /**
     * @param Page $page
     */
    protected function auth(Page $page)
    {
        $this->authorize('edit', $page);
    }

    /**
     * @param Page $page
     * @param Page $related
     */
    public function destroy(Page $page, Page $related)
    {
        $this->auth($page);
        $page->removeRelation($related);

        Event::fire(new PageRelationshipRemoved($page, $related));
    }

    /**
     * @return View
     */
    public function index(Page $page)
    {
        $this->auth($page);

        return view('boomcms::editor.page.settings.relations');
    }

    /**
     * @param Page $page
     * @param Page $related
     */
    public function store(Page $page, Page $related)
    {
        $this->auth($page);
        $page->addRelation($related);

        Event::fire(new PageRelationshipAdded($page, $related));
    }
}
