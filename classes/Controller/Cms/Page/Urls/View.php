<?php

class Controller_Cms_Page_Urls_View extends Controller_Cms_Page_Urls
{
    public function action_add()
    {
        $this->template = new View("$this->viewDirectory/add", array(
            'page' => $this->page,
        ));
    }

    public function action_move()
    {
        $this->template = new View("$this->viewDirectory/move", array(
            'url' => $this->page_url,
            'current' => $this->page_url->getPage(),
            'page' => $this->page,
        ));
    }
}
