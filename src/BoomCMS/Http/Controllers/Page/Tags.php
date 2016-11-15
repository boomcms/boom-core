<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Database\Models\Tag;
use BoomCMS\Events\PageHadTagAdded;
use BoomCMS\Events\PageHadTagRemoved;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Tag as TagFacade;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class Tags extends Controller
{
    /**
     * @param Request $request
     * @param Page    $page
     *
     * @return int
     */
    public function add(Request $request, Site $site, Page $page)
    {
        $this->auth($page);

        $name = $request->input('tag');
        $group = $request->input('group');
        $tag = TagFacade::findOrCreate($site, $name, $group);

        $page->addTag($tag);

        Event::fire(new PageHadTagAdded($page, $tag));

        return $tag->getId();
    }

    /**
     * @param Page $page
     */
    protected function auth(Page $page)
    {
        $this->authorize('edit', $page);
    }

    /**
     * @param Page $page
     */
    public function view(Site $site, Page $page)
    {
        $this->auth($page);

        $all = TagFacade::findBySite($site);
        $grouped = [];

        foreach ($all as $t) {
            $group = $t->getGroup() ?: '';
            $grouped[$group][] = $t;
        }

        $tags = Helpers::getTags($page)->map(function (Tag $tag) {
            return $tag->getId();
        });

        return view('boomcms::editor.page.settings.tags', [
            'all'  => $grouped,
            'tags' => $tags->toArray(),
        ]);
    }

    /**
     * @param Page $page
     * @param Tag  $tag
     */
    public function remove(Page $page, Tag $tag)
    {
        $this->auth($page);

        $page->removeTag($tag);

        Event::fire(new PageHadTagRemoved($page, $tag));
    }
}
