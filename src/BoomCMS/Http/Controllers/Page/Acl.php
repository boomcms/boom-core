<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events\PageRelationshipAdded;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Group as GroupFacade;
use Illuminate\Support\Facades\Event;
use Illuminate\View\View;

class Acl extends Controller
{
    /**
     * @param Page $page
     */
    protected function auth(Page $page)
    {
        $this->authorize('editAcl', $page);
    }

    /**
     * @param Page  $page
     * @param Group $group
     */
    public function destroy(Page $page, Group $group)
    {
        $this->auth($page);
        $page->removeAclGroup($group);
    }

    /**
     * View the page ACL settings.
     *
     * @param Page $page
     *
     * @return View
     */
    public function index(Page $page)
    {
        $this->auth();

        return view('boomcms::editor.page.settings.acl', [
            'page'      => $page,
            'allGroups' => GroupFacade::findAll(),
            'groupIds'  => $page->getAclGroupIds(),
        ]);
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
