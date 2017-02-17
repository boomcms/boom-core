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
     * @return string
     */
    public function getCopyright(): string
    {
        return $this->oneOf(['exif:Copyright'], '');
    }

    /**
     * {@inheritdoc}
     *
     * @return null|Carbon
     */
    public function getCreatedAt()
    {
        $timestamp = $this->oneOf(['exif:DateTimeOriginal', 'exif:DateTimeDigitized', 'exif:DateTime', 'date:create']);

        return !empty($timestamp) ? Carbon::parse($timestamp) : null;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->oneOf(['exif:ImageDescription'], '');
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
