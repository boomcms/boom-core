<?php

namespace BoomCMS\Link;

class External extends Link
{
    protected $link;

    public function __construct($link)
    {
        $this->link = $link;
    }

    public function getTitle()
    {
        return $this->url();
    }

    public function url()
    {
        return $this->link;
    }
}
