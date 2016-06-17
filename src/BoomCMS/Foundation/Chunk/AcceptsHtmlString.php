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

    protected function show()
    {
        if (is_callable($this->html)) {
            return call_user_func($this->html, $this);
        }

        return $this->addContentToHtml();
    }
}
