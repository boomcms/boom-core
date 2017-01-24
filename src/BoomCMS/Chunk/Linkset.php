<?php

namespace BoomCMS\Chunk;

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
    public function getLinks(): array
    {
        if ($this->links !== null) {
            return $this->links;
        }

        $this->links = $this->attrs['links'] ?? [];

        foreach ($this->links as $i => &$link) {
            $target = empty($link['target_page_id']) ? $link['url'] : $link['target_page_id'];

            $link = Link::factory($target, $link);

            if (!$this->editable && !$link->isVisible()) {
                unset($this->links[$i]);
            }
        }

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
