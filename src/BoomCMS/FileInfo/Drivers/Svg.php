<?php

namespace BoomCMS\FileInfo\Drivers;

class Svg extends Image
{
    public function getDimensions(): float
    {
        if ($this->dimensions === null) {
            $xml = simplexml_load_file($this->getPathname());
            $attrs = $xml->attributes();

            $this->dimensions = [
                (float) $xmlattributes->width,
                (float) $xmlattributes->height,
            ];
        }

        return $this->dimensions;
    }
}
