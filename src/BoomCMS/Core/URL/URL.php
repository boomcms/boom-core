<?php

namespace BoomCMS\Core\URL;

use BoomCMS\Core\Page\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Traits\HasId;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\URL as URLHelper;
use InvalidArgumentException;

class URL implements Arrayable
{
    use HasId;

    /**
     * @var array
     */
    private $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
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
     *
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

    /**
     * Get the URL using a given scheme.
     * 
     * @param string $scheme
     *
     * @return string
     */
    public function scheme($scheme)
    {
        return str_replace('http', $scheme, (string) $this);
    }

    public function setIsPrimary($isPrimary)
    {
        if (!is_bool($isPrimary)) {
            throw new InvalidArgumentException(__CLASS__.'::'.__METHOD__.' must only be called with a boolean argument');
        }

        $this->attributes['is_primary'] = $isPrimary;

        return $this;
    }

    public function setPageId($id)
    {
        $this->attributes['page_id'] = $id;

        return $this;
    }

    public function __toString()
    {
        $location = $this->getLocation();
        $location = (substr($location, 0) === '/') ? $location : '/'.$location;

        $url = URLHelper::to($location);

        return ($location === '/') ? $url.'/' : $url;
    }
}
