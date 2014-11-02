<?php

use Boom\Tag;
use Boom\Asset;

class Controller_Cms_Tags_Asset extends Controller_Cms_Tags
{
    public function before()
    {
        parent::before();

        if ($this->request->param('id') != 0) {
            $this->ids = array_unique(explode('-', $this->request->param('id')));
        }

        $this->authorization('manage_assets');
    }

    public function action_add()
    {
        $tag = Tag\Factory::findOrCreateByName($this->request->post('tag'));
        $tag->addToAssets($this->ids);
    }

    public function action_list()
    {
        $this->tags = array();
        $asset = Asset\Factory::byId($this->request->param('id'));

        if ( ! empty($this->ids)) {
            $finder = new Tag\Finder();
            $finder->addFilter(new Tag\Finder\Filter\Asset($asset));

            $this->tags = $finder->findAll();
        }

        $this->template = new View("boom/tags/list", array(
            'tags'    =>    $this->tags,
        ));

        $message = (count($this->tags)) ? 'asset.hastags' : 'asset.notags';
        $this->template->set('message', Kohana::message('boom', $message));
    }
}
