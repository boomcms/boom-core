<?php

namespace BoomCMS\FileInfo\Drivers;

class Svg extends Image
{
    public function getDimensions(): array
    {
        if ($this->dimensions === null) {
            $xml = simplexml_load_file($this->getPathname());
            $attrs = $xml->attributes();

            $this->dimensions = [
                (float) $attrs->width,
                (float) $attrs->height,
            ];
        }

        return $this->dimensions;
    }
}
