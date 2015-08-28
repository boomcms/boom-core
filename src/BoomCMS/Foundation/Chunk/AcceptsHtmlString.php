<?php

namespace BoomCMS\Foundation\Chunk;

trait AcceptsHtmlString
{
    protected $html = '';

    public function getHtmlTemplate()
    {
        return $this->html;
    }

    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }
}