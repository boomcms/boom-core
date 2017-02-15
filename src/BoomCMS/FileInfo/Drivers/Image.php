<?php

namespace BoomCMS\FileInfo\Drivers;

class Image extends DefaultDriver
{
    protected $dimensions;

    public function getDimensions(): array
    {
        if ($this->dimensions === null) {
            $this->dimensions = getimagesize($this->file->getPathname());
        }

        return $this->dimensions;
    }

    public function getHeight(): float
    {
        return $this->getDimensions()[1];
    }

    public function getWidth(): float
    {
        return $this->getDimensions()[0];
    }
}
