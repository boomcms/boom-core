<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use Illuminate\Support\Facades\View;

class Linkset extends BaseChunk
{
    protected $defaultTemplate = 'quicklinks';

    protected $links;

    public function __construct(Page $page, array $attrs, $slotname, $editable)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        if (isset($this->attrs['links'])) {
            foreach ($this->attrs['links'] as &$link) {
                $link = new Linkset\Link($link);
            }
        }
    }

    protected function show()
    {
        return View::make($this->viewPrefix."linkset.$this->template", [
            'title' => $this->getTitle(),
            'links' => $this->getLinks(),
        ])->render();
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
     * Removes internal links to delted pages.
     *
     * And internal links to invisible pages when the editor is disabled.
     *
     * @param array $links
     *
     * @return array
     */
    protected function removeInvalidLinks(array $links)
    {
        foreach ($links as $i => $item) {
            $link = $item->getLink();

            if ($link->isExternal()) {
                continue;
            }

            if (!$link->getPage() ||
                (!$this->editable && !$link->getPage()->isVisible())
            ) {
                unset($links[$i]);
            }
        }

        return $links;
    }
}
