<?php

namespace BoomCMS\Core\Asset\Type;

use BoomCMS\Core\Asset\Asset;

class Image extends Asset
{
    protected $type = 'Image';

    public function getAspectRatio()
    {
        return ($this->getHeight() > 0) ? ($this->getWidth() / $this->getHeight()) : 1;
    }

    public function getHeight()
    {
        return $this->get('height');
    }

    public function getWidth()
    {
        return $this->get('width');
    }

    public function isImage()
    {
        return true;
    }
}
