<?php

namespace BoomCMS\FileInfo\Drivers;

class Svg extends Image
{
    /**
     * @var array
     */
    protected $xmlAttrs;

    /**
     * {@inheritdoc}
     *
     * @return float
     */
    public function getAspectRatio(): float
    {
        $attrs = $this->getXmlAttrs();

        list($x, $y, $width, $height) = explode(' ', $attrs->viewBox);

        return $width / $height;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getDimensions(): array
    {
        if ($this->dimensions === null) {
            $attrs = $this->getXmlAttrs();

            $this->dimensions = [
                (float) $attrs->width,
                (float) $attrs->height,
            ];
        }

        return $this->dimensions;
    }

    /**
     * Reads the SVG file and returns the XML attributes as an object.
     *
     * @return object
     */
    protected function getXmlAttrs()
    {
        if ($this->xmlAttrs === null) {
            $xml = simplexml_load_file($this->file->getPathname());
            $this->xmlAttrs = $xml->attributes();
        }

        return $this->xmlAttrs;
    }
}
