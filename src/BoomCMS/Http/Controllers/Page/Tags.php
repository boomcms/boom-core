<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
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
    public function __construct(Page $page)
    {
        $this->authorize('edit', $page);
    }

    public function add(Request $request, Page $page)
    {
        $name = $request->input('tag');
        $group = $request->input('group');
        $tag = TagFacade::findOrCreateByNameAndGroup($name, $group);

        $page->addTag($tag);

        Event::fire(new PageHadTagAdded($page, $tag));

        return $tag->getId();
    }

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

    public function remove(Page $page, Tag $tag)
    {
        $page->removeTag($tag);

        Event::fire(new PageHadTagRemoved($page, $tag));
    }
}
