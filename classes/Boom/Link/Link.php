<?php

namespace Boom\Link;

abstract class Link
{
    public function __toString()
    {
        return (string) $this->url();
    }

    public static function factory($link)
    {
        return (ctype_digit($link) || substr($link, 0, 1) == '/') ? new Internal($link) : new External($link);
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
}
