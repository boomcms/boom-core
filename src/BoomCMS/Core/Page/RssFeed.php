<?php

namespace BoomCMS\Core\Page;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Helpers;
use Thujohn\Rss\Rss;

class RssFeed
{
    /**
     * @var Page\Page
     */
    private $page;

    /**
     * @param Page $page
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function addItem(Rss $feed, Page $page)
    {
        $tags = Helpers::getTags($page, 'Author');
        $authors = [];

        if (count($tags)) {
            foreach ($tags as $tag) {
                $authors[] = $tag->getName();
            }
        }

        $feed->item([
            'guid'              => $page->url(),
            'title'             => $page->getTitle(),
            'description|cdata' => $page->getDescription(),
            'link'              => $page->url(),
            'pubDate'           => $page->getVisibleFrom()->format('r'),
            'author|cdata'      => empty($authors) ? null : implode(',', $authors),
        ]);
    }

    public function getFeedItems()
    {
        return PageFacade::findByParentId($this->page->getId());
    }

    public function render()
    {
        $feed = new Rss();
        $feed
            ->feed('2.0', 'UTF-8')
            ->channel([
                'title'       => $this->page->getTitle(),
                'description' => $this->page->getDescription(),
                'link'        => $this->page->url(),
            ]);

        foreach ($this->getFeedItems() as $page) {
            $this->addItem($feed, $page);
        }

        return (string) $feed;
    }
}
