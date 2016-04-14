<?php

namespace BoomCMS\Link;

use BoomCMS\Support\Helpers\URL;

abstract class Link
{
    public function __toString()
    {
        return (string) $this->url();
    }

    public static function factory($link)
    {
        return (is_numeric($link) || URL::isInternal($link)) ?
            new Internal($link) : new External($link);
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
