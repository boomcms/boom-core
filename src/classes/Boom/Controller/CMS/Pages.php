<?php

namespace Boom\Controller\CMS;

use Boom\Page;

class Pages extends CMS
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

        $this->template = View::factory('boom/pages/index', [
            'pages'    =>    $pages,
        ]);
    }
}
