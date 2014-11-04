<?php

use Boom\Page;

class Controller_Page_Rss extends Boom\Controller\Page
{
    public function action_show()
    {
        $feed = new Page\RssFeed($this->page);

        $this->response
            ->headers('Content-Type', 'application/rss+xml')
            ->body($feed->render());
    }
}
