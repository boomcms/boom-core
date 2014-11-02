<?php

namespace Boom\Chunk;

use \Boom\Page\Page as Page;
use \Kohana as Kohana;
use \View as View;

class Tag extends \Boom\Chunk
{
    protected $_default_template = 'gallery';
    protected $_tag;
    protected $_type = 'tag';

    public function __construct(Page $page, $chunk, $editable = true)
    {
        parent::__construct($page, $chunk, $editable);

        $this->_tag = $this->_chunk->target;
    }

    protected function _show()
    {
        if ( ! $this->_template || ! Kohana::find_file("views", $this->viewDirectory."tag/$this->_template")) {
            $this->_template = $this->_default_template;
        }

        return View::factory($this->viewDirectory."tag/$this->_template", array(
            'tag' => $this->_tag,
        ));
    }

    protected function _show_default()
    {
        return new View($this->viewDirectory."default/tag/$this->_template");
    }

    public function attributes()
    {
        return array(
            $this->attributePrefix.'tag_id' => $this->get_tag()->id,
        );
    }

    public function get_tag()
    {
        return $this->_tag;
    }

    public function has_content()
    {
        return $this->_chunk->loaded() && $this->get_tag()->loaded();
    }
}
