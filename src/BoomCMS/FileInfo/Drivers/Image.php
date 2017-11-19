<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Exception;
use Imagick;

class Image extends DefaultDriver
{
    /**
     * @var array
     */
    protected $dimensions;

    public function getAssetType(): string
    {
        return 'image';
    }

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

        try {
            return !empty($timestamp) ? Carbon::parse($timestamp) : null;
        } catch (Exception $e) {
            return;
        }
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
            $im = $this->getImagick();

            $this->dimensions = [$im->getImageWidth(), $im->getImageHeight()];
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

    public function getImagick(): Imagick
    {
        $im = new Imagick();
        $im->readimageblob($this->read());

        return $im;
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
        $im = $this->getImagick();

        return $im->getImageProperties('');
    }
}
