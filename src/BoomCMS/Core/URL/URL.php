<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Core\Page\Page;
use BoomCMS\Core\Facades\Page as PageFacade;

use Illuminate\Support\Facades\URL as URLHelper;
use Illuminate\Contracts\Support\Arrayable;

use InvalidArgumentException;

class URL implements Arrayable
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

    public function toArray()
    {
        return $this->attrs;
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

    public function getPage()
    {
        return PageFacade::findById($this->getPageId());
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
		return trim($this->getLocation(), '/') === ltrim($location, '/');
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

    public function setIsPrimary($isPrimary)
    {
        if  ( !is_bool($isPrimary)) {
            throw new InvalidArgumentException(__CLASS__ . '::' . __METHOD__ . ' must only be called with a boolean argument');
        }

        $this->attrs['is_primary'] = $isPrimary;

        return $this;
    }

    public function setPageId($id)
    {
        $this->attrs['page_id'] = $id;

        return $this;
    }

    public function __toString()
    {
		$location = $this->getLocation();
        $location = (substr($location, 0) === '/') ? $location : '/' . $location;

        return URLHelper::to($location);
    }
}