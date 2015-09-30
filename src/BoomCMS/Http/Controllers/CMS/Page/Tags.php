<?php

namespace BoomCMS\Http\Controllers\CMS\Page;

use BoomCMS\Core\Auth\Auth;
use BoomCMS\Core\Tag;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class Tags extends Controller
{
    /**
     * @var Auth
     */
    public $auth;

    /**
     * @var Tag\Provider
     */
    protected $provider;

    public function __construct(Auth $auth, Request $request, Tag\Provider $provider)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->page = $request->route()->getParameter('page');
        $this->provider = $provider;

        $this->authorization('edit_page', $this->page);
    }

    public function add()
    {
        $tag = $this->provider->findOrCreateByNameAndGroup(
            $this->request->input('tag'),
            $this->request->input('group')
        );

        $this->page->addTag($tag);
    }

    public function listTags()
    {
        $tags = $this->page->getGroupedTags();
        $freeTags = isset($tags['']) ? $tags[''] : [];
        unset($tags['']);

        $groupSuggestions = $this->page->getTemplate()->getTagGroupSuggestions();
        $groupSuggestions = array_unique(array_merge(array_keys($tags), $groupSuggestions));
        sort($groupSuggestions);

        return View::make('boom::editor.page.settings.tags', [
            'tags'         => $tags,
            'freeTags'     => isset($freeTags) ? $freeTags : [],
            'groups'       => $groupSuggestions,
        ]);
    }

    public function remove()
    {
        $tag = $this->provider->byId($this->request->input('tag'));
        $this->page->removeTag($tag);
    }
}
