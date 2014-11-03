<?php

use Boom\Page;

class Controller_Cms_Pages extends Boom\Controller
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_pages');
    }

    public function action_index()
    {
        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\ParentId(null));
        $pages = $finder->findAll();

        $this->template = View::factory('boom/pages/index', array(
            'pages'    =>    $pages,
        ));
    }
}
