<?php

namespace BoomCMS\Core\Controllers\CMS;

use BoomCMS\Core\Page;
use BoomCMS\Core\Controller\Controller;

class Pages extends Controller
{
    public function __construct()
    {
        $this->authorization('manage_pages');
    }

    public function index(Page\Finder $finder)
    {
        $finder->addFilter(new Page\Finder\Filter\ParentId(null));
        $pages = $finder->findAll();

        return View::make('boom::pages.index', [
            'pages' => $pages,
        ]);
    }
}
