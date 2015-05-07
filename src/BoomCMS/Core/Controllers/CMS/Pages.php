<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Page;
use BoomCMS\Core\Controller\Controller;

class Pages extends Controller
{
    public function before()
    {
        parent::before();

        $this->authorization('manage_pages');
    }

    public function index()
    {
        $finder = new Page\Finder();
        $finder->addFilter(new Page\Finder\Filter\ParentId(null));
        $pages = $finder->findAll();

        $this->template = View::factory('boom/pages/index', [
            'pages'    =>    $pages,
        ]);
    }
}
