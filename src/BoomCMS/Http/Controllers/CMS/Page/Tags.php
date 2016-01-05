<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Events\PageHadTagAdded;
use BoomCMS\Events\PageHadTagRemoved;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Tag;
use BoomCMS\Support\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class Tags extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->page = $request->route()->getParameter('page');

        $this->authorization('edit_page', $this->page);
    }

    public function add()
    {
        $tag = Tag::findOrCreateByNameAndGroup(
            $this->request->input('tag'),
            $this->request->input('group')
        );

        $this->page->addTag($tag);
        Event::fire(new PageHadTagAdded($this->page, $tag));
    }

    public function listTags()
    {
        $grouped = [];
        $tags = Helpers::getTags($this->page);

        foreach ($tags as $t) {
            $group = $t->getGroup() ?: '';
            $grouped[$group][] = $t;
        }

        return view('boomcms::editor.page.settings.tags', [
            'tags' => $grouped,
        ]);
    }

    public function remove()
    {
        $tag = Tag::find($this->request->input('tag'));
        $this->page->removeTag($tag);

        Event::fire(new PageHadTagRemoved($this->page, $tag));
    }
}
