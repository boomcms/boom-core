<?php

namespace BoomCMS\Core\Link;

use BoomCMS\Support\Facades\Page;

class Internal extends Link
{
    /**
     *
     * @var Page\Page
     */
    protected $page;

    protected $urlFragment;

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
            $this->page = Page::findById($link);
        } else {
            $location = ($link === '/') ? '' : substr($link, 1);

            // Extract the query string and fragement
            $this->queryString = parse_url($link, PHP_URL_QUERY);
            $this->urlFragment = parse_url($link, PHP_URL_FRAGMENT);

            // Only get the path of the URL to ensure that if there's a query string it's ignored.
            $this->page = Page::findByUri(parse_url($location, PHP_URL_PATH));
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

    public function isValidPage()
    {
        return $this->page->loaded();
    }

    public function url()
    {
        // Return the URL of the page and append the fragment and query string if provided.
        $url = (string) $this->page->url();

        if ($this->urlFragment) {
            $url .= '#' . $this->urlFragment;
        }

        if ($this->queryString) {
            $url .= '?' . $this->queryString;
        }

        return $url;
    }
}
