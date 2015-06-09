<?php

namespace BoomCMS\Core\Controllers;

class Page extends Controller
{
    /**
     *
     * @var Page\Page
     */
    public $page;

    /**
     *
     * @var string
     */
    protected $responseBody;

    /**
     *
     * @var Template
     */
    public $template;

    public function show()
    {
        $page = $this->request->route()->getParameter('boomcms.currentPage');

        return $page->getTemplate()->getView();
    }
}
