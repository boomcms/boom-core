<?php

namespace BoomCMS\Link;

class External extends Link
{
    public function getTitle()
    {
        return $this->url();
    }

    public function url()
    {
        return $this->link;
    }
}
