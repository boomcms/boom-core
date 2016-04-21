<?php

namespace BoomCMS\Link;

use BoomCMS\Support\Facades\Page;
use BoomCMS\Support\Helpers\URL;

class Internal extends Link
{
    /**
     * @var string
     */
    protected $link;

    /**
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
        $this->link = $link;

        if (is_int($link) || ctype_digit($link)) {
            $this->page = Page::find($link);
        } else {
            // Extract the query string and fragement
            $this->queryString = parse_url($link, PHP_URL_QUERY);
            $this->urlFragment = parse_url($link, PHP_URL_FRAGMENT);

            $path = URL::getInternalPath($link);
            $this->page = Page::findByUri($path);
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
        if (!$this->page) {
            return $this->link;
        }

        // Return the URL of the page and append the fragment and query string if provided.
        $url = (string) $this->page->url();

        if ($this->urlFragment) {
            $url .= '#'.$this->urlFragment;
        }

        if ($this->queryString) {
            $url .= '?'.$this->queryString;
        }

        return $url;
    }
}
