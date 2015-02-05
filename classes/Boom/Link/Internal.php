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

    /**
     * Holds an optional query string for the link.
     *
     * If the URL text entry of the URL picker is used then a query string can be provided (e.g. for linking to a search results page)
     *
     * When this is done the query string needs to be ignored when finding the page with the provided URL.
     * And then appended to the page URL when the link is used.
     *
     * @var string
     */
    protected $queryString;

    public function __construct($link)
    {
        if (ctype_digit($link)) {
            $this->page = Page\Factory::byId($link);
        } else {
            $location = ($link === '/') ? '' : substr($link, 1);
            
            // Extract the query string, if there is one.
            $this->queryString = parse_url($link, PHP_URL_QUERY);

            // Only get the path of the URL to ensure that if there's a query string it's ignored.
            $this->page = Page\Factory::byUri(parse_url($location, PHP_URL_PATH));
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
        // Return the URL of the page and append the query string if one was provided.
        return $this->page->url() . (($this->queryString)? '?' . $this->queryString : '');
    }
}
