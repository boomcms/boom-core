<?php

use \Boom\Tag\Factory as TagFactory;

class Controller_Cms_Page_Tags extends Controller_Cms_Page
{
    public function before()
    {
        parent::before();

        $this->authorization('edit_page', $this->page);
    }

    public function add()
    {
        $tag = TagFactory::findOrCreateByNameAndGroup($this->request->input('tag'), $this->request->input('group'));
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

        $this->template = new View("boom/editor/page/settings/tags", [
            'tags' => $tags,
            'freeTags' => isset($freeTags) ? $freeTags : [],
            'groups' => $groupSuggestions,
        ]);
    }

    public function remove()
    {
        $tag = TagFactory::byId($this->request->input('tag'));
        $this->page->removeTag($tag);
    }
}
