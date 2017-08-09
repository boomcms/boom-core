<?php

namespace BoomCMS\Link;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\Chunk;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Helpers\URL;

class PageLink extends Internal
{
    /**
     * @var Page
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

    public function __construct($link, array $attrs = [])
    {
        parent::__construct($link, $attrs);

        if ($link instanceof Page) {
            $this->page = $link;
        } elseif (is_numeric($link)) {
            $this->page = PageFacade::find($link);
        } else {
            // Extract the query string and fragement
            $this->queryString = parse_url($link, PHP_URL_QUERY);
            $this->urlFragment = parse_url($link, PHP_URL_FRAGMENT);

            $path = URL::getInternalPath($link);
            $this->page = PageFacade::findByUri($path);
        }
    }

    protected function getContentFeatureImageId(): int
    {
        return $this->page->getFeatureImageId();
    }

    protected function getContentText(): string
    {
        return Chunk::get('text', 'standfirst', $this->page)->text();
    }

    protected function getContentTitle(): string
    {
        return $this->page->getTitle();
    }

    /**
     * @return int
     */
    public function getFeatureImageId(): int
    {
        $featureImageId = parent::getFeatureImageId();

        return $featureImageId > 0 ? $featureImageId : $this->page->getFeatureImageId();
    }

    public function getPage()
    {
        return $this->page;
    }

    public function isValid(): bool
    {
        return $this->page !== null && !$this->page->isDeleted();
    }

    /**
     * Whether the link is visible.
     *
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->page->isVisible();
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
