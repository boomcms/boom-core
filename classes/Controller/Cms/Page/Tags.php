<?php

use \Boom\Tag\Factory as TagFactory;

class Controller_Cms_Page_Tags extends Controller_Cms_Page
{
    public function before()
    {
        parent::before();

        $this->authorization('edit_page', $this->page);
    }

    public function action_add()
    {
        $tag = TagFactory::findOrCreateByNameAndGroup($this->request->post('tag'), $this->request->post('group'));
        $this->page->addTag($tag);
    }

    public function action_list()
    {
        $tags = $this->page->getGroupedTags();
        $freeTags = isset($tags[''])? $tags[''] : array();
        unset($tags['']);

        $groupSuggestions = $this->page->getTemplate()->getTagGroupSuggestions();
        $groupSuggestions = array_unique(array_merge(array_keys($tags), $groupSuggestions));
        sort($groupSuggestions);

        $this->template = new View("boom/editor/page/settings/tags", [
            'tags' => $tags,
            'freeTags' => isset($freeTags)? $freeTags : array(),
            'groups' => $groupSuggestions,
        ]);
    }

    public function action_remove()
    {
        $tag = TagFactory::byId($this->request->post('tag'));
        $this->page->removeTag($tag);
    }
}
