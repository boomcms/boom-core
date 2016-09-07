<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Contracts\Models\Group;
use BoomCMS\Database\Models\Page;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Facades\Group as GroupFacade;
use Illuminate\Http\Request;
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
     * @param Page $page
     * @param Group $group
     */
    public function destroy(Page $page, Group $group)
    {
        $this->auth($page);

        PageFacade::recurse($page, function($p) use($group) {
            $p->removeAclGroupId($group->getId());
        });
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
        $this->auth($page);

        return view("boomcms::editor.page.settings.acl", [
            'page'      => $page,
            'allGroups' => GroupFacade::findAll(),
            'groupIds'  => $page->getAclGroupIds(),
        ]);
    }

    /**
     * @param Page $page
     * @param Group $group
     */
    public function store(Page $page, Group $group)
    {
        $this->auth($page);

        PageFacade::recurse($page, function($p) use($group) {
            $p->addAclGroupId($group->getId());
        });
    }

    /**
     * @param Request $request
     * @param Page $page
     */
    public function update(Request $request, Page $page)
    {
        $this->auth($page);

        $enabled = ($request->input('enabled') === '1');

        PageFacade::recurse($page, function($p) use($enabled) {
            $p->setAclEnabled($enabled);

            PageFacade::save($p);
        });
    }
}
