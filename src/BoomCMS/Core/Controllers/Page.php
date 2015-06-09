<?php

namespace BoomCMS\Core\Controllers;

class Page extends Controller
{
    public function show()
    {
        $page = $this->request->route()->getParameter('boomcms.currentPage');

        return $page->getTemplate()->getView();
    }
}
