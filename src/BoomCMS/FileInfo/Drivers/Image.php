<?php

namespace BoomCMS\FileInfo\Drivers;

class Image extends DefaultDriver
{
    /**
     * @var array
     */
    protected $dimensions;

    /**
     * Get the dimensions (width and height) of an image as an array.
     *
     * @return array
     */
    public function getDimensions(): array
    {
        if ($this->dimensions === null) {
            $this->dimensions = getimagesize($this->file->getPathname());
        }

        return $this->dimensions;
    }

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    public function getHeight(): float
    {
        return $this->getDimensions()[1];
    }

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    public function getWidth(): float
    {
        return $this->getDimensions()[0];
    }
}
