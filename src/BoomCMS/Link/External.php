<?php

namespace BoomCMS\Link;

class External extends Link
{
    public function getTitle(): string
    {
        return $this->attrs['title'] ?? $this->url();
    }

    public function url()
    {
        return $this->link;
    }

    /**
     * @return bool
     */
    public function isExternal(): bool
    {
        return true;
    }
}
