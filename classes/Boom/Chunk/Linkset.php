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
        if ( ! Editor::instance()->isDisabled()) {
            // Editor is enabled, show all the links.
            $links = $this->_chunk->links();
        } else {
            // Editor is disabled - only show links where the target page is visible
            $links = [];

            foreach ($this->_chunk->links() as $link) {
                if ($link->is_external() || $link->target->isVisible()) {
                    $links[] = $link;
                }
            }
        }

        return new View($this->viewDirectory."linkset/$this->_template", [
            'title'        =>    $this->_chunk->title,
            'links'    =>    $links,
        ]);
    }

    public function _show_default()
    {
        return new View($this->viewDirectory . "default/linkset/$this->_template");
    }

    public function hasContent()
    {
        return count($this->_chunk->links()) > 0;
    }
}
