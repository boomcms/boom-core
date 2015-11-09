<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Events\PageHadTagAdded;
use BoomCMS\Events\PageHadTagRemoved;
use BoomCMS\Http\Controllers\Controller;
use BoomCMS\Support\Facades\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;

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
        $tags = $this->page->getGroupedTags();
        $freeTags = isset($tags['']) ? $tags[''] : [];
        unset($tags['']);

        $groupSuggestions = $this->page->getTemplate()->getTagGroupSuggestions();
        $groupSuggestions = array_unique(array_merge(array_keys($tags), $groupSuggestions));
        sort($groupSuggestions);

        return View::make('boomcms::editor.page.settings.tags', [
            'tags'         => $tags,
            'freeTags'     => isset($freeTags) ? $freeTags : [],
            'groups'       => $groupSuggestions,
        ]);
    }

    public function remove()
    {
        $tag = Tag::byId($this->request->input('tag'));
        $this->page->removeTag($tag);

        Event::fire(new PageHadTagRemoved($this->page, $tag));
    }
}
