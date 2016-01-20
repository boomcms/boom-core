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
    protected $role = 'edit';

    /**
     * @param Request $request
     * @param Page    $page
     *
     * @return int
     */
    public function add(Request $request, Site $site, Page $page)
    {
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
    public function view(Page $page)
    {
        $grouped = [];
        $tags = Helpers::getTags($page);

        foreach ($tags as $t) {
            $group = $t->getGroup() ?: '';
            $grouped[$group][] = $t;
        }

        return view('boomcms::editor.page.settings.tags', [
            'tags' => $grouped,
        ]);
    }

    /**
     * @param Page $page
     * @param Tag  $tag
     */
    public function remove(Page $page, Tag $tag)
    {
        $page->removeTag($tag);

        Event::fire(new PageHadTagRemoved($page, $tag));
    }
}
