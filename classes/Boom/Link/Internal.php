<?php

namespace Boom\Link;

use Boom\Page;

class Internal extends Link
{
    /**
	 *
	 * @var Page\Page
	 */
    protected $page;

    public function __construct($link)
    {
        if (ctype_digit($link)) {
            $this->page = Page\Factory::byId($link);
        } else {
            $location = ($link === '/') ? $link : substr($link, 1);
            $this->page = Page\Factory::byUri($location);
        }
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getTitle()
    {
        return $this->page->getTitle();
    }

    public function url()
    {
        return $this->page->url();
    }
}
