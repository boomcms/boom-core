<?php

namespace BoomCMS\FileInfo\Drivers;

class Svg extends Image
{
    /**
     * @var array
     */
    protected $xmlAttrs;

    /**
     * {@inheritDoc}
     *
     * @return float
     */
    public function getAspectRatio(): float
    {
        $attrs = $this->getXmlAttrs();

        if (!isset($attrs->viewBox)) {
            return 0;
        }

        list($x, $y, $width, $height) = explode(' ', $attrs->viewBox);

        return $width / $height;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function getDimensions(): array
    {
        if ($this->dimensions === null) {
            $attrs = $this->getXmlAttrs();

            $this->dimensions = [
                (float) $attrs->width ?? 0,
                (float) $attrs->height ?? 0,
            ];
        }

        return $this->dimensions;
    }

    /**
     * Reads the SVG file and returns the XML attributes as an object
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

    public function readMetadata(): array
    {
        return [];
    }
}
