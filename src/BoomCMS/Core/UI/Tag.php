<?php

namespace BoomCMS\Core\UI;

use BoomCMS\Core\Tag\Tag as TagObject;

class Tag extends AbstractUIElement
{
    /**
     *
     * @var TagObject
     */
    private $tag;

    /**
     *
     * @param TagObject $tag
     */
    public function __construct(TagObject $tag)
    {
        $this->tag = $tag;
    }

    public function render()
    {
        return "<li class='b-tag'><span>{$this->tag->getName()}</span><a href='#' data-tag='{$this->tag->getId()}' class='fa fa-trash-o b-tag-remove'></a></li>";
    }
}
