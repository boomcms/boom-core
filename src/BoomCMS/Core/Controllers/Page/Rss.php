<?php

namespace BoomCMS\Core\Controllers\Page;

class Rss extends Boom\Controller\Page
{
    public function before()
    {
        parent::before();

        $this->response->headers('Content-Type', 'application/rss+xml');
    }
}
