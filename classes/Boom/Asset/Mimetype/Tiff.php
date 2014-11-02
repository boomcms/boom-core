<?php

namespace Boom\Asset\Mimetype;

use \Boom\Asset as Asset;

class Tiff extends Asset\Mimetype
{
    protected $_extension = 'tiff';
    protected $_type = \Boom\Asset\Type::IMAGE;
}
