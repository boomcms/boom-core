<?php

namespace BoomCMS\Core\Link;

abstract class Link
{
    public function __toString()
    {
        return (string) $this->url();
    }

    public static function factory($link)
    {
        if (ctype_digit($link) || substr($link, 0, 1) == '/') {
            $internal = new Internal($link);

            // If it's not a valid CMS URL then it's a relative URL which isn't CMS managed so treat it as an external URL.
            return ($internal->isValidPage()) ? $internal : new External($link);
        } else {
            return new External($link);
        }
    }

    public function isExternal()
    {
        return $this instanceof External;
    }

    public function isInternal()
    {
        return $this instanceof Internal;
    }

    abstract public function url();

    abstract public function getTitle();
}
