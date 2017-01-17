<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Link\Link;
use Illuminate\Support\Facades\View;

class Linkset extends BaseChunk
{
    protected $defaultTemplate = 'quicklinks';

    /**
     * @var int
     */
    protected $limit = 0;

    /**
     * @var links
     */
    protected $links;

    /**
     * @var array
     */
    protected $options = [
        'title'      => false,
        'link-text'  => false,
        'link-title' => false,
        'link-asset' => false,
    ];

    public function __construct(Page $page, array $attrs, $slotname)
    {
        parent::__construct($page, $attrs, $slotname);

        if (isset($this->attrs['links'])) {
            foreach ($this->attrs['links'] as &$link) {
                $target = empty($link['target_page_id']) ? $link['url'] : $link['target_page_id'];

                $link = Link::factory($target, $link);
            }
        }
    }

    /**
     * @return array
     */
    public function attributes()
    {
        $attrs = [
            $this->attributePrefix.'limit' => $this->limit,
        ];

        foreach ($this->options as $key => $value) {
            $attrs[$this->attributePrefix.$key] = $value;
        }

        return $attrs;
    }

    /**
     * Make this a 'feature' linkset.
     *
     * Used for backwards compatibility for feature boxes and link chunks
     *
     * Sets limit to 1 and enables all link options
     *
     * @return $this
     */
    public function feature(): Linkset
    {
        $this->limit = 1;

        foreach (['link-text', 'link-title', 'link-asset'] as $option) {
            $this->options[$option] = true;
        }

        return $this;
    }

    /**
     * Returns an array of links in the linkset.
     *
     * Or an empty array if the linkset doesn't contain any links
     *
     * @return array
     */
    public function getLinks()
    {
        if ($this->links !== null) {
            return $this->links;
        }

        $this->links = isset($this->attrs['links']) ?
            $this->removeInvalidLinks($this->attrs['links']) : [];

        return $this->links;
    }

    /**
     * Returns true if the linkset contains any links.
     *
     * @return bool
     */
    public function hasContent()
    {
        return count($this->getLinks()) > 0;
    }

    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
    }

    /**
     * Removes internal links to deleted pages.
     *
     * And internal links to invisible pages when the editor is disabled.
     *
     * @param array $links
     *
     * @return array
     */
    protected function removeInvalidLinks(array $links)
    {
        foreach ($links as $i => $link) {
            if ($link->isExternal()) {
                continue;
            }

            $page = $link->getPage();

            if (!$page || $page->isDeleted() ||
                (!$this->editable && !$page->isVisible())
            ) {
                unset($links[$i]);
            }
        }

        return $links;
    }

    /**
     * Set linkset options.
     *
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options): Linkset
    {
        $this->options = $options + $this->options;

        return $this;
    }

    protected function show()
    {
        $links = $this->getLinks();

        return View::make($this->viewPrefix."linkset.$this->template", [
            'title'  => $this->getTitle(),
            'links'  => $links,
            'target' => $links[0],
        ]);
    }
}
