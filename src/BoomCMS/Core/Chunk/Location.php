<?php

namespace BoomCMS\Core\Chunk;

use Illuminate\Support\Facades\View;

class Location extends BaseChunk
{
     protected $type = 'location';

    public function hasContent()
    {
        return $this->getLat() && $this->getLng();
    }

    public function getAddress()
    {
        return isset($this->attrs['address']) ? $this->attrs['title'] : '';
    }

    public function getLat()
    {
        return isset($this->attrs['lat']) ? $this->attrs['lat'] : 0;
    }

    public function getLng()
    {
        return isset($this->attrs['lng']) ? $this->attrs['lng'] : 0;
    }

    public function getPostcode()
    {
        return isset($this->attrs['postcode']) ? $this->attrs['postcode'] : '';
    }

    public function getTitle()
    {
        return isset($this->attrs['title']) ? $this->attrs['title'] : '';
    }

    /**
	* Show a chunk where the target is set.
	*/
    public function show()
    {
        return View::make($this->viewPrefix."feature.$this->template", [
            'lat' => $this->getLat(),
            'lng' => $this->getLng(),
            'address' => $this->getAddress(),
            'title' => $this->getTitle(),
            'postcode' => $this->getPostcode(),
        ]);
    }

    public function showDefault()
    {
        return View::make($this->viewPrefix."default.location.$this->template", [
            'placeholder' => $this->getPlaceholderText()
        ]);
    }
}
