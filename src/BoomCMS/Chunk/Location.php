<?php

namespace BoomCMS\Chunk;

use Illuminate\Support\Facades\View;
use Lootils\Geo\Location as GeoLocation;

class Location extends BaseChunk
{
    /**
     * Whether the address section should be enabled.
     *
     * @var bool
     */
    protected $address = false;

    /**
     * a GeoLocation object for the location's lat/lng.
     *
     * @var GeoLocation
     */
    protected $location;

    /**
     * Whether the title section should be enabled.
     *
     * @var bool
     */
    protected $title;

    /**
     * Enable the address section.
     *
     * @return $this
     */
    public function address()
    {
        $this->address = true;

        return $this;
    }

    /**
     * Returns the array of chunk attributes.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            $this->attributePrefix.'address' => (int) $this->address,
            $this->attributePrefix.'title'   => (int) $this->title,
        ];
    }

    /**
     * Returns the value for the address field.
     *
     * @return string
     */
    public function getAddress()
    {
        return isset($this->attrs['address']) ? $this->attrs['address'] : '';
    }

    /**
     * Returns the latitude portion of the location.
     *
     * @return float
     */
    public function getLat()
    {
        return isset($this->attrs['lat']) ? $this->attrs['lat'] : 0;
    }

    /**
     * Returns the longitude portion of the location.
     *
     * @return float
     */
    public function getLng()
    {
        return isset($this->attrs['lng']) ? $this->attrs['lng'] : 0;
    }

    /**
     * Returns a Lootils\Geo\Location object for the current lat/lng.
     *
     * @return GeoLocation
     */
    public function getLocation()
    {
        if ($this->location === null) {
            $this->location = new GeoLocation($this->getLat(), $this->getLng());
        }

        return $this->location;
    }

    /**
     * Returns the value of the postcode field.
     *
     * @return string
     */
    public function getPostcode()
    {
        return isset($this->attrs['postcode']) ? $this->attrs['postcode'] : '';
    }

    /**
     * Returns the value of the title field.
     *
     * @return string
     */
    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return $this->getLat() != 0 && $this->getLng() != 0;
    }

    /**
     * Show a chunk where the target is set.
     */
    public function show()
    {
        return View::make($this->viewPrefix."location.$this->template", [
            'lat'      => $this->getLat(),
            'lng'      => $this->getLng(),
            'address'  => $this->getAddress(),
            'title'    => $this->getTitle(),
            'postcode' => $this->getPostcode(),
            'location' => $this->getLocation(),
        ]);
    }

    /**
     * Enable the title section.
     *
     * @return $this
     */
    public function title()
    {
        $this->title = true;

        return $this;
    }
}
