<?php

namespace Boom\Page;

use Thujohn\Rss\Rss;

class RssFeed
{
    /**
     *
     * @var Page\Page
     */
    private $page;

    /**
     *
     * @param \Boom\Page\Page\Page $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function getFeedItems()
    {
        $finder = new Finder;
        $finder->addFilter(new Finder\Filter\ParentPage($this->page));

        return $finder->findAll();
    }

    public function render()
    {
        $feed = Rss::feed('2.0', 'UTF-8');

        $feed->channel(array(
            'title' => $this->page->getTitle(),
            'description' => $this->page->getDescription(),
            'link' => $this->page->url()
        ));

        foreach ($this->getFeedItems() as $page) {
            $feed->item(array(
                'title' => $page->getTitle(),
                'description|cdata' => $page->getDescription(),
                'link' => $page->url(),
            ));
        }

        return $feed->render();
    }
}