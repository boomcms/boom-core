<?php

use Boom\Asset;

class Controller_Cms_Assets_Tags extends Controller_Cms_Assets
{
    public function before()
    {
        parent::before();
    }

    public function action_list()
    {
        $collection = new Asset\Collection(explode('-', $this->request->param('id')));

        $this->template = new View('boom/assets/tags', [
            'tags' => $collection->getTags()
        ]);
    }

    public function action_add()
    {
        $this->_csrf_check();

        $collection = new Asset\Collection($this->request->post('assets'));
        $collection->addTag($this->request->post('tag'));
    }

    public function action_remove()
    {
        $this->_csrf_check();

        $collection = new Asset\Collection($this->request->post('assets'));
        $collection->removeTag($this->request->post('tag'));
    }
}
