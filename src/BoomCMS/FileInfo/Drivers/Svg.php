<?php

namespace BoomCMS\FileInfo\Drivers;

class Svg extends Image
{
    protected $xmlAttrs;

    public function getAspectRatio(): float
    {
        $attrs = $this->getXmlAttrs();

        list($x, $y, $width, $height) = explode(' ', $attrs->viewBox);

        return $width / $height;
    }

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

    protected function getXmlAttrs()
    {
        if ($this->xmlAttrs === null) {
            $xml = simplexml_load_file($this->file->getPathname());
            $this->xmlAttrs = $xml->attributes();
        }

        return $this->xmlAttrs;
    }
}
