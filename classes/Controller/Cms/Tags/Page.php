<?php

use \Boom\Page\Factory as PageFactory;
use \Boom\Tag\Factory as TagFactory;
use \Boom\Tag\Finder as TagFinder;

class Controller_Cms_Tags_Page extends Controller_Cms_Tags
{
    public function before()
    {
        parent::before();

        $this->model = PageFactory::byId($this->request->param('id'));
        $this->ids = array($this->model->getId());

        $this->authorization('edit_page', $this->model);
    }

    public function action_add()
    {
        $tag = TagFactory::findOrCreateByName($this->request->post('tag'));
        $tag->addToPages($this->ids);
    }

    public function action_list()
    {
        $this->tags = array();

        if ( ! empty($this->ids)) {
            $finder = new TagFinder();
            $finder->addFilter(new TagFinder\Filter\Page($this->model));

            $this->tags = $finder->findAll();
        }

        $this->template = new View("boom/tags/list", array(
            'tags'    =>    $this->tags,
        ));

        $message = (count($this->tags)) ? 'page.hastags' : 'page.notags';
        $this->template->set('message', Kohana::message('boom', $message));
    }

    public function action_remove()
    {
        $tag = TagFactory::byName($this->request->post('tag'));
        $tag->removeFromPages($this->ids);
    }
}
