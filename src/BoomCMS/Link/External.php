<?php

namespace BoomCMS\Link;

class External extends Link
{
    protected $_link;

    public function __construct($link)
    {
        $this->_link = $link;
    }

    public function getTitle()
    {
        return $this->url();
    }

    public function url()
    {
        return $this->_link;
    }
}
