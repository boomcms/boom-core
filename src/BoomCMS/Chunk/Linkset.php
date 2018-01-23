<?php

namespace BoomCMS\Chunk;

use BoomCMS\Link\Link;
use Illuminate\Support\Facades\View;

class Linkset extends BaseChunk
{
    protected $defaultTemplate = 'quicklinks';

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
        'limit'      => 0,
    ];

    /**
     * @return array
     */
    public function attributes()
    {
        foreach ($this->options as $key => $value) {
            $attrs[$this->attributePrefix.$key] = (int) $value;
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
    public function feature(): self
    {
        return $this->setOptions([
            'link-text'  => true,
            'link-title' => true,
            'link-asset' => true,
            'limit'      => 1,
        ]);
    }

    /**
     * Returns an array of links in the linkset.
     *
     * Or an empty array if the linkset doesn't contain any (valid, visible) links
     *
     * @param int $limit
     *
     * @return array
     */
    public function getLinks(int $limit = 0): array
    {
        if ($this->links === null) {
            $links = $this->attrs['links'] ?? [];
            $this->links = $this->removeInvalidOrHiddenLinks($links);
        }

        return $limit > 0 ? array_slice($this->links, 0, $limit) : $this->links;
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
     * Removes links which are:.
     *
     *  * Invalid because they don't have a target page ID or a URL to link to
     *  * Hidden because they're an internal link but the page isn't visible to the current user
     *
     * @param array $links
     *
     * @return array
     */
    protected function removeInvalidOrHiddenLinks(array $links): array
    {
        foreach ($links as $i => &$link) {
            if (!isset($link['target_page_id']) && !isset($link['url'])) {
                unset($links[$i]);

                continue;
            }

            $target = empty($link['target_page_id']) ? $link['url'] : $link['target_page_id'];

            $link = Link::factory($target, $link);

            if (!$link->isValid() || (!$this->editable && !$link->isVisible())) {
                unset($links[$i]);
            }
        }

        return array_values($links);
    }

    /**
     * Sets a limit on the number of links which are displayed.
     *
     * This is an alias for the setOptions() method.
     *
     * The specified limit is not enforced by the linkset editor (unless the limit is 1)
     * Rather, setting a limit will ensure that the $links variable in the chunk view will contain a subset of the linkset links.
     * This may be useful when displaying the first n links of a linkset in a different page.
     *
     * When the limit is set to 1 then the editor ensures that only a single link is inserted.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit(int $limit): self
    {
        return $this->setOptions(['limit' => $limit]);
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options + $this->options;

        return $this;
    }

    protected function show()
    {
        $links = $this->getLinks($this->options['limit']);

        return View::make($this->viewPrefix."linkset.$this->template", [
            'title'  => $this->getTitle(),
            'links'  => $links,
            'target' => $links[0],
        ]);
    }
}
