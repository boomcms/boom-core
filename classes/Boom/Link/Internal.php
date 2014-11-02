<?php

namespace Boom\Link;

class Internal extends Link
{
    /**
	 *
	 * @var \Model_Page
	 */
    protected $page;

    public function __construct($link)
    {
        if (ctype_digit($link)) {
            $this->page = new \Model_Page($link);
        } else {
            $location = ($link === '/') ? $link : substr($link, 1);
            $this->page = \ORM::factory('Page_URL', array('location' => $location))->page;
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
