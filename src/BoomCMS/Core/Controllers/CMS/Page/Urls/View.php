<?php

class Controller_Cms_Page_Urls_View extends Controller_Cms_Page_Urls
{
    public function add()
    {
        return View::make("$this->viewPrefix/add", [
            'page' => $this->page,
        ]);
    }

    public function move()
    {
        return View::make("$this->viewPrefix/move", [
            'url' => $this->page_url,
            'current' => $this->page_url->getPage(),
            'page' => $this->page,
        ]);
    }
}
