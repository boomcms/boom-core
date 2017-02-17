<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Imagick;

class Image extends DefaultDriver
{
    /**
     * @var array
     */
    protected $dimensions;

    /**
     * {@inheritdoc}
     *
     * @return null|Carbon
     */
    public function getCreatedAt()
    {
        $metadata = $this->getMetadata();
        $keys = ['date:create', 'exif:DateTimeOriginal', 'exif:DateTimeDigitized'];

        foreach ($keys as $key) {
            if (isset($metadata[$key])) {
                return Carbon::parse($metadata[$key]);
            }
        }
    }

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

    /**
     * Extracts metadata data from an image.
     *
     * @return array
     */
    public function readMetadata(): array
    {
        $im = new Imagick($this->file->getPathname());

        return $im->getImageProperties('');
    }
}
