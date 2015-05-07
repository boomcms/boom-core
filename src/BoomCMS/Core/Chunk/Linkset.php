<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Editor\Editor as Editor;
use \View as View;

class Linkset extends \Boom\Chunk
{
    protected $_default_template = 'quicklinks';
    protected $_type = 'linkset';

    protected $links;

    protected function _show()
    {
        return new View($this->viewPrefix."linkset/$this->_template", [
            'title' => $this->_chunk->title,
            'links' => $this->getLinks(),
        ]);
    }

    public function _show_default()
    {
        return new View($this->viewPrefix . "default/linkset/$this->_template");
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
