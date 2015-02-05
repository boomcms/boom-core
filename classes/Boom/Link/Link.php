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
        if (ctype_digit($link) || substr($link, 0, 1) == '/') {
            $url = new \Model_Page_URL(['location' => parse_url($link, PHP_URL_PATH)]);

            // If it's not a valid CMS URL then it's a relative URL which isn't CMS managed so treat it as an external URL.
            return ($url->loaded()) ? new Internal($link) : new External($link);
        } else {
            return new External($link);
        }
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
    abstract public function getTitle();
}
