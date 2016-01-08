<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Database\Models\Site;
use BoomCMS\Events\PageHadTagAdded;
use BoomCMS\Events\PageHadTagRemoved;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Tag;
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
        $tag = Tag::findOrCreate($site, $request->input('tag'), $request->input('group'));
        $page->addTag($tag);

        Event::fire(new PageHadTagAdded($this->page, $tag));

        return $tag->getId();
    }

    /**
     * @param Page $page
     */
    public function listTags(Page $page)
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
     * @param Request $request
     * @param Page    $page
     */
    public function remove(Request $request, Page $page)
    {
        $tag = Tag::find($request->input('tag'));
        $page->removeTag($tag);

        Event::fire(new PageHadTagRemoved($page, $tag));
    }
}
