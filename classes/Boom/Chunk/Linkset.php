<?php

namespace Boom\Chunk;

use Boom\Editor\Editor as Editor;
use \View as View;

class Linkset extends \Boom\Chunk
{
    protected $_default_template = 'quicklinks';
    protected $_type = 'linkset';

    protected function _show()
    {
        $links = $this->getLinks();

        if (Editor::instance()->isDisabled()) {
            foreach ($links as &$link) {
                if ($link->isInternal() && ! $link->getLink()->getPage()->isVisible()) {
                    unset($link);
                }
            }
        }

        return new View($this->viewDirectory."linkset/$this->_template", [
            'title' => $this->_chunk->title,
            'links' => $links,
        ]);
    }

    public function _show_default()
    {
        return new View($this->viewDirectory . "default/linkset/$this->_template");
    }

    public function getLinks()
    {
        return $this->_chunk->links();
    }

    public function hasContent()
    {
        return count($this->getLinks()) > 0;
    }
}
