<?php

namespace BoomCMS\Link;

use BoomCMS\Support\Helpers\URL;

abstract class Link
{
    /**
     * Array of query string parameters in the link.
     *
     * @var null|mixed
     */
    protected $query;

    /**
     * @var string
     */
    protected $link;

    /**
     * @param string $link
     */
    public function __construct($link)
    {
        $this->link = $link;
    }

    public function __toString()
    {
        return (string) $this->url();
    }

    public static function factory($link)
    {
        return (is_numeric($link) || URL::isInternal($link)) ?
            new Internal($link) : new External($link);
    }

    abstract public function getTitle();

    /**
     * Returns the hostname for the link target.
     *
     * @return string
     */
    public function getHostname()
    {
        return parse_url($this->url(), PHP_URL_HOST);
    }

    /**
     * Returns a query string parameter for a given key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getParameter($key)
    {
        $query = $this->getQuery();

        return isset($query[$key]) ? $query[$key] : null;
    }

    /**
     * Returns an array of query string parameters in the URL.
     *
     * @return array
     */
    public function getQuery()
    {
        if ($this->query === null) {
            $string = parse_url($this->link, PHP_URL_QUERY);

            parse_str($string, $this->query);
        }

        return $this->query;
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
