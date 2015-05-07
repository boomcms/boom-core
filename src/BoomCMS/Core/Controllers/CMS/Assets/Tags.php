<?php

use BoomCMS\Core\Asset;

class Controller_Cms_Assets_Tags extends Controller_Cms_Assets
{
    public function before()
    {
        parent::before();
    }

    public function listTags()
    {
        $collection = new Asset\Collection(explode('-', $this->request->param('id')));

        $this->template = new View('boom/assets/tags', [
            'tags' => $collection->getTags()
        ]);
    }

    public function add()
    {
        $collection = new Asset\Collection($this->request->input('assets'));
        $collection->addTag($this->request->input('tag'));
    }

    public function remove()
    {
        $collection = new Asset\Collection($this->request->input('assets'));
        $collection->removeTag($this->request->input('tag'));
    }
}
