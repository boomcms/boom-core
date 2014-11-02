<?php

namespace Boom\Chunk;

use Boom\Editor\Editor as Editor;
use \View as View;

class Slideshow extends \Boom\Chunk
{
    protected $_default_template = 'circles';

    protected $_type = 'slideshow';

    protected function _show()
    {
        return new View($this->viewDirectory . "slideshow/$this->_template", array(
            'chunk'    =>    $this->_chunk,
            'title'        =>    $this->_chunk->title,
            'slides'    =>    $this->_chunk->slides(),
            'editor'    =>    Editor::instance(),
        ));
    }

    public function _show_default()
    {
        return new View($this->viewDirectory."default/slideshow/$this->_template");
    }

    public function hasContent()
    {
        return $this->_chunk->loaded() && count($this->_chunk->slides()) > 0;
    }

    public function slides()
    {
        return $this->_chunk->slides();
    }

    public function thumbnail()
    {
        return $this->_chunk->thumbnail();
    }
}
