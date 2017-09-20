<?php

namespace BoomCMS\Link;

class External extends Link
{
    public function getTitle(): string
    {
        if (isset($this->attrs['title']) && !empty($this->attrs['title'])) {
            return $this->attrs['title'];
        }

        return preg_replace('~^https?://|mailto:|tel:~', '', $this->url());
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

    /**
     * Whether the link is valid.
     *
     * @return bool
     */
    public function isValid(): bool
    {
        return !empty($this->link) && $this->link !== '#';
    }
}
