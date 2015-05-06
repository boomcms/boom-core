<?php

namespace BoomCMS\Core\UI;

class Tag extends AbstractUIElement
{
    /**
     *
     * @var \Boom\Tag\Tag
     */
    private $tag;

    /**
     *
     * @param \Boom\Tag\Tag $tag
     */
    public function __construct(\Boom\Tag\Tag $tag)
    {
        $this->tag = $tag;
    }

    public function render()
    {
        return "<li class='b-tag'><span>{$this->tag->getName()}</span><a href='#' data-tag='{$this->tag->getId()}' class='b-tag-remove'></a></li>";
    }
}
