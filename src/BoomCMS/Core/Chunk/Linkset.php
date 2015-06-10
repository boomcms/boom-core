<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Editor\Editor as Editor;
use \View as View;

class Linkset extends BaseChunk
{
    protected $_default_template = 'quicklinks';
    protected $_type = 'linkset';

    protected $links;

    protected function show()
    {
        return new View($this->viewPrefix."linkset/$this->template", [
            'title' => $this->_chunk->title,
            'links' => $this->getLinks(),
        ]);
    }

    public function showDefault()
    {
        return new View($this->viewPrefix . "default/linkset/$this->template");
    }

    public function getLinks()
    {
        if ($this->links === null) {
            $this->links = $this->_chunk->links();

            if ( ! Editor::instance()->isEnabled()) {
                foreach ($this->links as $i => $link) {
                    if ($link->isInternal() && ! $link->getLink()->getPage()->isVisible()) {
                        unset($this->links[$i]);
                    }
                }
            }
        }

        return $this->links;
    }

    public function hasContent()
    {
        return count($this->getLinks()) > 0;
    }
}
