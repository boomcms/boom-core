<?php

namespace BoomCMS\Core\Asset\Mimetype;

class UnsupportedMimeType extends \UnexpectedValueException
{
    private $mimetype;

    public function __construct($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    public function getMimetype()
    {
        return $this->mimetype;
    }
}
