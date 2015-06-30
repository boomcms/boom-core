<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\View;

class Linkset extends BaseChunk
{
    protected $defaultTemplate = 'quicklinks';
    protected $type = 'linkset';

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
        return View::make($this->viewPrefix . "linkset.$this->template", [
            'title' => $this->getTitle(),
            'links' => $this->getLinks(),
        ]);
    }

    public function showDefault()
    {
        return View::make($this->viewPrefix . "default.linkset.$this->template", [
            'placeholder' => $this->getPlaceholderText()
        ]);
    }

    public function getLinks()
    {
        if ($this->links === null) {
            if (isset($this->attrs['links'])) {
                $this->links = $this->attrs['links'];

                if ( ! $this->editable) {
                    foreach ($this->links as $i => $link) {
                        if ($link->isInternal() && ! $link->getLink()->getPage()->isVisible()) {
                            unset($this->links[$i]);
                        }
                    }
                }
            } else {
                $this->links = [];
            }
        }

        return $this->links;
    }

    public function hasContent()
    {
        return count($this->getLinks()) > 0;
    }

    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
    }
}
