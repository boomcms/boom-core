<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Core\Page\Page;
use Illuminate\Support\Facades\URL as URLHelper;

class URL
{
    /**
     *
     * @var array
     */
    private $attrs;

    public function __construct(array $attrs)
    {
        $this->attrs = $attrs;
    }

    public function get($key)
    {
        return isset($this->attrs[$key]) ? $this->attrs[$key] : null;
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getLocation()
    {
        return $this->get('location');
    }

    public function getPageId()
    {
        return $this->get('page_id');
    }

    /**
     * Determine whether this URL matches a given URL.
     *
     * @param string $location
     * @return bool
     */
    public function is($location)
    {
        return $this->getLocation() === $location;
    }

    public function isForPage(Page $page)
    {
        return $this->getPageId() === $page->getId();
    }

    public function isPrimary()
    {
        return $this->get('is_primary') == true;
    }

    public function loaded()
    {
        return $this->getId() > 0;
    }

    public function __toString()
    {
        return URLHelper::to($this->getLocation());
    }
}