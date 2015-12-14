<?php

namespace BoomCMS\Database\Models;

use BoomCMS\Contracts\Models\Page as PageInterface;
use BoomCMS\Contracts\Models\URL as URLInterface;
use BoomCMS\Support\Helpers\URL as URLHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL as URLFacade;
use InvalidArgumentException;

class URL extends Model implements URLInterface
{
    const ATTR_ID = 'id';
    const ATTR_PAGE_ID = 'page_id';
    const ATTR_LOCATION = 'location';
    const ATTR_IS_PRIMARY = 'is_primary';

    /**
     * @var PageInterface
     */
    protected $page;

    protected $table = 'page_urls';

    public $guarded = [
        self::ATTR_ID,
    ];

    public $timestamps = false;

    public function __toString()
    {
        $location = $this->getLocation();
        $location = (substr($location, 0) === '/') ? $location : '/'.$location;

        $url = URLFacade::to($location);

        return ($location === '/') ? $url.'/' : $url;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->{self::ATTR_ID};
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->{self::ATTR_LOCATION};
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        if ($this->page === null) {
            $this->page = $this->hasOne(Page::class, 'id')->first();
        }

        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return (int) $this->{self::ATTR_PAGE_ID};
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

    /**
     * @param Page $page
     *
     * @return bool
     */
    public function isForPage(PageInterface $page)
    {
        return $this->getPageId() === $page->getId();
    }

    /**
     * @return bool
     */
    public function isPrimary()
    {
        return $this->{self::ATTR_IS_PRIMARY} == true;
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

    /**
     * @param bool $isPrimary
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function setIsPrimary($isPrimary)
    {
        if (!is_bool($isPrimary)) {
            throw new InvalidArgumentException(__CLASS__.'::'.__METHOD__.' must only be called with a boolean argument');
        }

        $this->{self::ATTR_IS_PRIMARY} = $isPrimary;

        return $this;
    }

    /**
     * @param string $value
     */
    public function setLocationAttribute($value)
    {
        $this->attributes[self::ATTR_LOCATION] = URLHelper::sanitise($value);
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setPageId($id)
    {
        $this->{self::ATTR_PAGE_ID} = $id;

        return $this;
    }
}
